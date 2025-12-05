<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Filament\Resources\PagoResource\RelationManagers;
use App\Models\ContratoPersona;
//use App\Models\ItemPersona;
use App\Models\DestinoPago;
use App\Models\Pago;
use App\Models\Personal;
use App\Models\TipoComprobante;
use App\Services\CronogramaService;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
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
use Illuminate\Support\Facades\Auth;
use Closure;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = 'Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                DatePicker::make('fecha_emision')
                    ->label('Fecha de pago')
                    ->live(onBlur: true)
                    ->rules(['required', 'date'])
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        // Limpiar cronograma cuando cambie la fecha
                        $set('cronograma_actual', null);
                        $set('deuda_total', 0);
                        // Si no es admin, sincronizar fecha_calculo con fecha_emision
                        //if (!Auth::user()->hasRole('admin')) {
                            $set('fecha_calculo', $state);
                        //}
                    }),
                DatePicker::make('fecha_calculo')
                    ->label('Fecha de calculo')
                    ->rules(['required', 'date'])
                    // ->visible(fn(Get $get): bool => Auth::user()->hasRole('admin'))
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        // Limpiar cronograma cuando cambie la fecha
                        $set('cronograma_actual', null);
                        $set('deuda_total', 0);
                    }),
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
                    ->required()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        $set('cronograma_actual', null);
                        $set('deuda_total', 0);
                    })
                    ->suffixAction(
                        Action::make('getSchedule')
                            ->icon('heroicon-o-arrow-path')
                            ->action(function (Get $get, Set $set, $state) {
                                $set('cronograma_actual', null);
                                $set('deuda_total', 0);
                                if ($state) {
                                    $service = app(CronogramaService::class);
                                    // Validar que la fecha_calculo no esté vacía
                                    if (!$get('fecha_calculo')) {
                                        Notification::make()
                                            ->title('¡Advertencia!')
                                            ->body('Debe seleccionar una fecha.')
                                            ->warning()
                                            ->send();
                                        return;
                                    }
                                    // Validar que la fecha sea válida
                                    try {
                                        $fechaValidada = Carbon::parse($get('fecha_calculo'))->format('Y-m-d');
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->title('¡Error!')
                                            ->body('La fecha seleccionada no es válida.')
                                            ->danger()
                                            ->send();
                                        return;
                                    }
                                    $cronograma = $service->getCurrent($state, $fechaValidada);
                                    if ($cronograma) {
                                        $set('cronograma_actual', json_decode(json_encode($cronograma), true));
                                        $total = collect($cronograma)->sum('total');
                                        $set('deuda_total', sprintf("%.2f", $total));
                                    }
                                }
                            })
                    ),
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
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $deudaTotal = $get('deuda_total');

                            // Validar que haya una deuda calculada
                            if (!$deudaTotal || $deudaTotal == 0) {
                                $fail('Debe calcular el cronograma antes de ingresar el importe.');
                                return;
                            }

                            $deudaTotal = (float) $deudaTotal;
                            $valorPago = (float) $value;

                            if ($valorPago > $deudaTotal) {
                                $fail("El importe (S/ {$valorPago}) no puede ser mayor a la deuda total (S/ {$deudaTotal}).");
                            }
                        }
                    ])
                    ->validationMessages([
                        'regex' => 'El valor debe ser numérico con hasta 2 decimales.',
                        'gte' => 'El valor debe ser mayor o igual a cero.',
                    ]),
                TextInput::make('deuda_total')
                    ->label('Total del cronograma')
                    ->disabled()
                    ->required(),
                TextInput::make('referencia')
                    ->label('Referencia')
                    ->maxLength(255)
                    ->rules(['nullable', 'string'])
                    ->columnSpan('full')
                    ->validationMessages([
                        'max' => 'La referencia no puede exceder los 250 caracteres.',
                    ]),
                TableRepeater::make('cronograma_actual')
                    ->headers([
                        Header::make('cuota')->label('N°')->width('10px'),
                        Header::make('inicio')->label('F. Ini.')->width('55px'),
                        Header::make('vencimiento')->label('F. Ven.')->width('55px'),
                        Header::make('saldo')->label('Saldo')->width('50px'),
                        Header::make('capital')->label('Capital')->width('50px'),
                        Header::make('interes')->label('Interés')->width('50px'),
                        Header::make('mora')->label('Mora')->width('50px'),
                        Header::make('total')->label('Total')->width('50px'),
                    ])
                    ->emptyLabel('No hay registros')
                    ->schema([
                        TextInput::make('cuota')
                            ->readOnly(),
                        TextInput::make('inicio')
                            ->readOnly(),
                        TextInput::make('vencimiento')
                            ->readOnly(),
                        TextInput::make('saldo')
                            ->readOnly(),
                        TextInput::make('capital')
                            ->readOnly(),
                        TextInput::make('interes')
                            ->readOnly(),
                        TextInput::make('mora')
                            ->readOnly(),
                        TextInput::make('total')
                            ->label('Total')
                            ->readOnly(),
                    ])
                    ->streamlined(true)
                    ->defaultItems(0)
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columnSpan('full')
                    ->hiddenLabel(true)
                    ->extraAttributes([
                        'style' => 'max-height: 250px; overflow-y: auto;',
                    ])
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
                //Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('updatePay')
                    ->form([
                        TextInput::make('numero_operacion')
                            ->label('N° operación')
                            ->required(),
                        DateTimePicker::make('fecha_operacion')
                            ->label('Fecha operación')
                            ->required(),
                        Select::make('destino_pago')
                            ->label('Destino pago')
                            ->options(DestinoPago::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->required(),
                        FileUpload::make('voucher')
                            ->label('Voucher')
                            ->directory('pagos/vouchers')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(2048)
                            ->required()
                            ->storeFiles(false),
                    ])
                    ->action(function (array $data, Pago $record): void {
                        $record->numero_operacion = $data['numero_operacion'];
                        $record->fecha_operacion = $data['fecha_operacion'];
                        $record->destino_pago_id = $data['destino_pago'];
                        $file = $data['voucher'];

                        // Generar nombre personalizado
                        $fileName = sprintf(
                            'pago_%s_%s.%s',
                            $record->id,
                            now()->format('YmdHis'),
                            $file->getClientOriginalExtension()
                        );
                        // Guardar con nombre personalizado
                        $filePath = $file->storeAs('pagos/vouchers', $fileName, 'public');

                        $record->save();
                    }),
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
            'index' => Pages\ManagePagos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tipo_ingreso', '=', 1);
    }
}
