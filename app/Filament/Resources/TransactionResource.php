<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Pricing;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Customers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Wizard::make([

                    Forms\Components\Wizard\Step::make('Product and Price')
                    ->schema([
                        Grid::make(2)
                        ->schema([

                            Forms\Components\Select::make('pricing_id')
                            ->relationship('pricing', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $pricing = Pricing::find($state);

                                $price = $pricing->price;
                                $duration = $pricing->duration;

                                $subTotal = $price * $state;
                                $totalPpn = $subTotal * 0.11;
                                $totalAmount = $subTotal + $totalPpn;

                                $set('total_tax_amount', $totalPpn);
                                $set('grand_total_amount', $totalAmount);
                                $set('sub_total_amount', $price);
                                $set('duration', $duration);
                            })
                            ->afterStateHydrated(function (callable $set, $state) {
                                $pricingId = $state;
                                if ($pricingId) {
                                    $pricing = Pricing::find($pricingId);
                                    $duration = $pricing->duration;
                                    $set('duration', $duration);
                                }
                            }),

                            Forms\Components\TextInput::make('duration')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->prefix('Months'),


                        ]),

                        Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('sub_total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->readOnly(),

                            Forms\Components\TextInput::make('total_tax_amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->readOnly(),

                            Forms\Components\TextInput::make('grand_total_amount')
                            ->required()
                            ->numeric()
                            ->prefix('IDR')
                            ->readOnly()
                            ->helperText('Harga sudah include PPN 11%'),
                        ]),

                        Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('started_at')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $duration = $get('duration');
                                if ($state && $duration) {
                                    $endedAt = \Carbon\Carbon::parse($state)->addMonth($duration);
                                    $set('ended_at', $endedAt->format('Y-m-d'));
                                }
                            })
                            ->required(),

                            Forms\Components\DatePicker::make('ended_at')
                            ->readOnly()
                            ->required(),

                        ]),
                    ]),

                    Forms\Components\Wizard\Step::make('Customer Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                        ->relationship('student', 'email')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $user = User::find($state);

                            $name = $user->name;
                            $email = $user->email;

                            $set('name', $name);
                            $set('email', $email);
                        })
                        ->afterStateHydrated(function (callable $set, $state) {
                            $userId = $state;
                            if ($userId) {
                                $user = User::find($userId);
                                $name = $user->name;
                                $email = $user->email;
                                $set('name', $name);
                                $set('email', $email);
                            }
                        }),
                        Forms\Components\TextInput::make('name')
                        ->required()
                        ->readOnly()
                        ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                        ->required()
                        ->readOnly()
                        ->maxLength(255),
                    ]),


                Forms\Components\Wizard\Step::make('Payment Information')
                ->schema([

                    ToggleButtons::make('is_paid')
                    ->label('Apakah sudah membayar?')
                    ->boolean()
                    ->grouped()
                    ->icons([
                        true => 'heroicon-o-pencil',
                        false => 'heroicon-o-clock',
                    ])
                    ->required(),

                Forms\Components\Select::make('payment_type')
                ->options([
                    'Midtrans' => 'Midtrans',
                    'Manual' => 'Manual'
                ])
                ->required(),

                Forms\Components\FileUpload::make('proof')
                ->image(),
             ]),

                ])
                ->columnSpan('full')
                ->columns(1)
                ->skippable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\ImageColumn::make('student.photo')
                ->circular()
                ,

                Tables\Columns\TextColumn::make('student.name')
                ->searchable(),

                Tables\Columns\TextColumn::make('booking_trx_id')
                ->searchable(),

                Tables\Columns\TextColumn::make('pricing.name'),

                Tables\Columns\IconColumn::make('is_paid')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Terverivikasi'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('approve')
                ->label('Approve')
                ->action(function (Transaction $record) {
                    $record->is_paid = true;
                    $record->save();

                    Notification::make()
                    ->title('Order Approved')
                    ->success()
                    ->body('The Order has been successfully approved. ')
                    ->send();
                })
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Transaction $record) => !$record->is_paid),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
