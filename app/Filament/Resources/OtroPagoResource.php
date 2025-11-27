<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtroPagoResource\Pages;
use App\Filament\Resources\OtroPagoResource\RelationManagers;
use App\Models\ContratoPersona;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\TipoComprobante;
use App\Services\CronogramaService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class OtroPagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Pagos';

    protected static ?string $navigationLabel = 'Otros pagos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                DatePicker::make('fecha_emision')
                    ->label('Fecha de pago')
                    ->live(onBlur: true)
                    ->rules(['required', 'date']),
                Select::make('contrato_id')
                    ->label('Contrato')
                    ->getSearchResultsUsing(function (string $search) {
                        return ContratoPersona::query()
                            ->where(function ($query) use ($search) {
                                $query->where(function ($q) use ($search) {
                                    $q->where('rol_id', 1)
                                        ->whereHas('persona', function ($q) use ($search) {
                                            $q->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                                ->orWhere('numero_documento', 'like', "%{$search}%");
                                        });
                                })
                                    ->orWhere(function ($q) use ($search) {
                                        $q->where('rol_id', 1)
                                            ->where('contrato_id', 'like', "{$search}");
                                    });
                            })
                            ->whereHas('contrato', function ($query) {
                                $query->whereIn('estado_id', [1]);
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(function ($contratoPersona) {
                                $persona = $contratoPersona->persona;
                                return [$contratoPersona->contrato_id => "{$contratoPersona->contrato_id} - {$persona->full_name}"];
                            })
                            ->toArray();
                    })
                    ->getOptionLabelUsing(function ($value) {
                        $contratoPersona = ContratoPersona::where('contrato_id', $value)
                            ->where('rol_id', 1)
                            ->with('persona')
                            ->first();
                        return $contratoPersona ? "{$contratoPersona->contrato_id} - {$contratoPersona->persona->full_name}" : null;
                    })
                    ->searchable()
                    ->live()
                    ->required(),
                Select::make('oficina_id')
                    ->label('Oficina')
                    ->searchable()
                    ->relationship('oficina', 'nombre')
                    ->preload()
                    ->required(),
                TextInput::make('recibo')
                    ->label('N° Recibo')
                    ->rules(['required', 'string'])
                    ->validationMessages([
                        'required' => 'El número de recibo es obligatorio.',
                        'max' => 'El número de recibo no puede exceder los 50 caracteres.',
                    ]),
                Select::make('tipo_comprobante_id')
                    ->label('Tipo de comprobante')
                    ->options(TipoComprobante::all()->pluck('nombre', 'id')),
                TextInput::make('serie_numero')
                    ->label('N° Comprobante')
                    ->rules(['string'])
                    ->validationMessages([
                        'max' => 'El número de recibo no puede exceder los 50 caracteres.',
                    ])
                    ->requiredWith('tipo_comprobante_id'),
                Select::make('producto_id')
                    ->label('Producto')
                    ->options(Producto::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('medio_pago_id')
                    ->label('Medio pago')
                    ->searchable()
                    ->relationship('medioPago', 'nombre')
                    ->preload()
                    ->required(),
                TextInput::make('importe')
                    ->label('Importe')
                    ->required()
                    ->live()
                    ->rules([
                        'required',
                        'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                        'gte:0', // mayor o igual a 0
                    ])
                    ->validationMessages([
                        'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                        'gte' => 'El valor debe ser mayor o igual a cero.',
                    ]),
                TextInput::make('referencia')
                    ->label('Referencia')
                    ->maxLength(255)
                    ->rules(['nullable', 'string'])
                    ->columnSpan('full')
                    ->validationMessages([
                        'max' => 'La referencia no puede exceder los 250 caracteres.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->searchable(),
                TextColumn::make('fecha_emision')->label('Fecha')->date('d/m/Y'),
                TextColumn::make('importe')->label('Importe'),
                TextColumn::make('contrato_id')->label('Contrato'),
                TextColumn::make('contrato.rolTitular.id')->label('Titular')
                    ->formatStateUsing(fn ($record) => $record->contrato?->rolTitular?->full_name)
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('contrato.rolTitular', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', $search);
                        });
                    }),
                IconColumn::make('estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                IconColumn::make('referencia')
                    ->label('Referencia')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->tooltip(fn($record) => $record->referencia)
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->default(0)
                    ->options([
                        '0' => 'Activo',
                        '1' => 'Eliminado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc')
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageOtroPagos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo_ingreso', 2);
    }
}
