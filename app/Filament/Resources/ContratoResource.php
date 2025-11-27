<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContratoResource\Pages;
use App\Filament\Resources\ContratoResource\RelationManagers;
use App\Models\Categoria;
use App\Models\Contrato;
use App\Models\ContratoPersona;
use App\Models\Oficina;
use App\Models\Persona;
use App\Models\Producto;
use App\Models\Rol;
use App\Models\TipoContrato;
use Awcodes\TableRepeater\Components\TableRepeater;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Awcodes\TableRepeater\Header;
use Closure;
class ContratoResource extends Resource
{
    protected static ?string $model = ContratoPersona::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static  ?string $navigationGroup = 'Plataforma';

    protected static ?int $navigationSort = 1;


    public static function getNavigationLabel(): string
    {
        return 'Contratos';
    }
    public static function getBreadcrumb(): string
    {
        return 'Contratos';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('General')
                    ->schema([
                        Select::make('oficina_id')
                            ->label('Oficina')
                            ->options(Oficina::all()->pluck('nombre', 'id'))
                            ->required(),
                        DatePicker::make('fecha_contrato')
                            ->label('Fecha de contrato')
                            ->required(),
                        Select::make('tipo_contrato_id')
                            ->label('Tipo de contrato')
                            ->options(TipoContrato::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('numero_contrato')
                            ->label('Número de contrato')
                            ->required(),
                        DateTimePicker::make('fecha_atencion')
                            ->label('Fecha de atención')
                            ->required(),
                        Select::make('categoria_id')
                            ->label('Categoría')
                            ->options(Categoria::all()->pluck('nombre', 'id'))
                            ->required(),
                        Select::make('personal_id')
                            ->relationship(
                                name: 'personal',
                                modifyQueryUsing: fn (Builder $query, $record) =>
                                $query->where('estado', true)
                                    ->when($record, function ($query) use ($record) {
                                        return $query->orWhere('id', $record->personal_id);
                                    })
                            )
                            ->getOptionLabelFromRecordUsing(fn (Model $record) =>
                                "{$record->full_name}" .
                                (!$record->estado ? ' (Inactivo)' : '')
                            )
                            ->preload()
                    ])
                ->columns(3),
                Fieldset::make('Financiamiento')
                    ->schema([
                        TextInput::make('total')
                            ->label('Total')
                            ->rules([
                                'required',
                                'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                                'gte:0', // mayor o igual a 0
                            ])
                            ->validationMessages([
                                'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                                'gte' => 'El valor debe ser mayor o igual a cero.',
                            ]),
                        TextInput::make('inicial')
                            ->label('Inicial')
                            ->rules([
                                'required',
                                'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                                'gte:0', // mayor o igual a 0
                            ])
                            ->validationMessages([
                                'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                                'gte' => 'El valor debe ser mayor o igual a cero.',
                            ]),
                        TextInput::make('descuento')
                            ->label('Descuento')
                            ->rules([
                                'required',
                                'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                                'gte:0', // mayor o igual a 0
                            ])
                            ->validationMessages([
                                'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                                'gte' => 'El valor debe ser mayor o igual a cero.',
                            ]),
                        TextInput::make('tea')
                            ->label('TEA')
                            ->rules([
                                'required',
                                'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                                'gte:0', // mayor o igual a 0
                            ])
                            ->validationMessages([
                                'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                                'gte' => 'El valor debe ser mayor o igual a cero.',
                            ]),
                        TextInput::make('numero_cuotas')
                            ->integer()
                            ->label('Número de cuotas')
                            ->required(),
                        Select::make('dia')
                            ->label('Día de Pago')
                            ->options(array_combine(range(1, 31), range(1, 31)))
                            ->required(),
                        DatePicker::make('fecha_vencimiento')
                            ->label('Fecha de primer vencimiento')
                            ->required()
                            ->after('fecha_contrato')
                            ->validationMessages([
                                'after' => 'La fecha de primer vencimiento debe ser posterior a la fecha de contrato.',
                                'required' => 'Este campo es obligatorio.',
                            ]),
//                        Toggle::make('excluir_inicial')
//                            ->label('Excluir inicial del cronograma')
//                            ->default(false)
//                            ->columnSpan(3),
                    ])
                    ->columns(3),
                TableRepeater::make('contratoPersonas')
                    ->headers([
                        Header::make('persona_id')->label('Persona')->width('140px'),
                        Header::make('rol_id')->label('Rol')->width('50px')
                    ])
                    ->relationship('contratoPersonas') // Define la relación
                    ->schema([
                        Select::make('persona_id')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array =>
                            Persona::whereRaw("CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', 'like', "%{$search}%")
                                ->get()
                                ->mapWithKeys(fn ($persona) => [$persona->id => $persona->full_name])
                                ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => Persona::find($value)?->full_name)
                            ->label('Persona')
                            ->required(),
                        Select::make('rol_id')
                            ->label('Rol')
                            ->options(Rol::all()->pluck('nombre', 'id'))
                            ->required()
                    ])
                    ->addActionLabel('Agregar Persona')
                    ->label('Personas')
                    ->minItems(2)
                    ->columnSpan('full')
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            // $value es el array del repeater
                            $titulares = collect($value)->filter(fn ($item) => $item['rol_id'] == 1);

                            if ($titulares->count() === 0) {
                                $fail('Debe haber al menos un titular.');
                            }

                            if ($titulares->count() > 1) {
                                $fail('Solo puede haber un titular.');
                            }
                        },
                    ]),
                TableRepeater::make('productos')
                    ->headers([
                        Header::make('producto_id')->label('Productos')->width('200px'),
                        Header::make('cantidad')->label('Cantidad')->width('150px')
                    ])
                    ->relationship('productos') // Define la relación
                    ->schema([
                        Select::make('producto_id')
                            ->label('Producto')
                            ->options(Producto::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),
                        TextInput::make('cantidad')
                            ->rules([
                                'required',
                                'regex:/^\d+(\.\d{1,2})?$/', // hasta 2 decimales, punto como separador
                                'gt:0', // mayor o igual a 0
                            ])
                            ->validationMessages([
                                'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                                'gt' => 'El valor debe ser mayor a cero.',
                            ])
                    ])
                    ->addActionLabel('Agregar producto')
                    ->label('Productos')
                    ->defaultItems(1)
                    ->columnSpan('full'),
                TableRepeater::make('convenios')
                    ->headers([
                        Header::make('persona_id')->label('Convenio')->width('140px'),
                    ])
                    ->relationship('convenios') // Define la relación
                    ->schema([
                        Select::make('persona_convenio_id')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array =>
                            Persona::whereRaw("CONCAT_WS(' ', nombre, primer_apellido, segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', 'like', "%{$search}%")
                                ->get()
                                ->mapWithKeys(fn ($persona) => [$persona->id => $persona->full_name])
                                ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => Persona::find($value)?->full_name)
                            ->label('Persona')
                            ->required(),
                    ])
                    ->addActionLabel('Agregar convenio')
                    ->label('Convenios')
                    ->columnSpan('full')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contrato_id')->searchable()->label('ID'),
                TextColumn::make('contrato.fecha_contrato')->label('Fecha')->date('d/m/Y'),
                TextColumn::make('contrato.tipo_contrato_id')->label('Tipo')
                    ->formatStateUsing(fn ($record) => $record->contrato->tipoContrato?->nombre),
                TextColumn::make('persona.nombre')
                    ->label('Nombres y Apellidos')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('persona', function ($query) use ($search) {
                            $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                                ->orWhere('numero_documento', "$search")
                                ->orWhere('direccion', 'like', "%{$search}%");
                        });
                    })
                    ->formatStateUsing(function ($record) {
                        return $record->persona->full_name;
                    }),
                TextColumn::make('contrato.estado_id')->label('Estado')
                    ->formatStateUsing(fn ($record) => $record->contrato->estado?->nombre )
                    ->badge()
                    ->color(fn ($record): string => match ($record->contrato->estado?->id) {
                        1 => 'success', // Vigente
                        2 => 'gray',    // Cancelado
                        3 => 'warning',  // Anulado
                        10 => 'danger', // Refinanciado
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('rol_id')
                    ->label('Rol')
                    ->relationship('rol', 'nombre')
                    ->default(1)
                    ->preload(),
                SelectFilter::make('contrato.estado_id')
                    ->label('Estado')
                    ->multiple()
                    ->relationship('contrato.estado', 'nombre')
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('contrato_id', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContratos::route('/'),
            'create' => Pages\CreateContrato::route('/create'),
            'view' => Pages\ViewContrato::route('/{record}'),
            'edit' => Pages\EditContrato::route('/{record}/edit'),
        ];
    }

    public static function getSlug(): string
    {
        return 'contratos';
    }
}
