<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';

    protected static ?string $navigationGroup = 'Configuraciones';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->rule(Password::defaults())
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => $state ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->label('Password'),
                Select::make('personal_id')
                    ->label('Personal')
                    ->searchable()
                    ->required()
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
                    ->preload(),
                CheckboxList::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->options(fn () => Role::pluck('name', 'id'))
                    ->columns(2)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('personal.nombre')
                    ->label('Personal')
                    ->formatStateUsing(fn($record) => $record->personal?->full_name)
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'super-admin',
                        'warning' => 'payment',
                        'info' => 'platform',
                    ])
                    ->badge()
             ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
