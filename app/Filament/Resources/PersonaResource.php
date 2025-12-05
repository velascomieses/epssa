<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonaResource\Pages;
use App\Filament\Resources\PersonaResource\RelationManagers;
use App\Models\EstadoCivil;
use App\Models\Persona;
use App\Models\TipoDocumentoIdentidad;
use App\Models\Ubigeo;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rules\Unique;

class PersonaResource extends Resource
{
    protected static ?string $model = Persona::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static  ?string $navigationGroup = 'Plataforma';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_documento_identidad_id')
                    ->label('Tipo de documento')
                    ->options(TipoDocumentoIdentidad::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('numero_documento')
                    ->label('N° Documento')
                    ->default(null)
                    ->rules([
                        'required',
                        'max:15',
                    ])
                    ->unique(
                        column: 'numero_documento',
                        modifyRuleUsing: function (Unique $rule, Get $get){
                            return $rule->where('tipo_documento_identidad_id', $get('tipo_documento_identidad_id'));
                        },
                        ignoreRecord: true
                    ),
                TextInput::make('primer_apellido')
                    ->label('Primer apellido')
                    ->maxLength(50)
                    ->default(null)
                    ->requiredIf('tipo_documento_identidad_id', '!=', 3),
                TextInput::make('segundo_apellido')
                    ->label('Segundo apellido')
                    ->maxLength(50)
                    ->default(null)
                    ->requiredIf('tipo_documento_identidad_id', '!=', 3),
                TextInput::make('nombre')
                    ->maxLength(245)
                    ->default(null)
                    ->required(),
                Select::make('sexo')
                    ->label('Sexo')
                    ->options([
                        'F' => 'Femenino',
                        'M' => 'Masculino',
                    ])
                    ->required(),
                Select::make('estado_civil_id')
                    ->label('Estado Civil')
                    ->options(EstadoCivil::all()->pluck('nombre', 'id'))
                    ->searchable(),
                DatePicker::make('fecha_nacimiento'),
                Select::make('ubigeo_id')
                    ->label('Ubigeo')
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return Ubigeo::query()
                            ->distritos()
                            ->where('nombre', 'like', "%{$search}%")
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn ($record) => [
                                $record->id => $record->full_name
                            ]);
                    })
                    ->getOptionLabelUsing(fn ($value) => Ubigeo::find($value)?->full_name),
                TextInput::make('direccion')
                    ->maxLength(150)
                    ->default(null),
                TextInput::make('telefono')
                    ->tel()
                    ->maxLength(45)
                    ->default(null),
                TextInput::make('correo_electronico')
                    ->maxLength(150)
                    ->default(null),
                Toggle::make('es_convenio')
                    ->label('Convenio'),
                Toggle::make('es_proveedor')
                    ->label('Proveedor')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nombre')
                    ->label('Nombres y apellidos')
                    ->formatStateUsing(fn ($record) => "{$record->full_name}")
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereRaw("CONCAT(nombre, ' ', primer_apellido, ' ', segundo_apellido) LIKE ?", ["%{$search}%"])
                            ->orWhere('numero_documento',$search);
                    }),
                TextColumn::make('sexo')
                    ->formatStateUsing(fn ($state) => $state === 'F' ? 'Mujer' : 'Hombre')
                    ->searchable(),
                TextColumn::make('fecha_nacimiento')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('numero_documento')
                    ->label('N° Documento')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->searchable(),
                TextColumn::make('telefono')
                    ->searchable()
            ])
            ->filters([
                //
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_audit_id'] = auth()->id();
                        return $data;
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->action(function ($record) {
                        try {
                            $record->delete();
                            Notification::make()
                                ->title('Registro eliminado con éxito.')
                                ->success()
                                ->send();
                        } catch (QueryException $exception) {
                            Notification::make()
                                ->title('Error al eliminar.')
                                ->body('No se puede eliminar este registro porque está relacionado con otros datos.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePersonas::route('/'),
        ];
    }
}
