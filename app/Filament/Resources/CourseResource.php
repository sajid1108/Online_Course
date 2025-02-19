<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Fieldset::make('Details')
                ->schema([
                    // ...
                    Forms\Components\TextInput::make('name')
                    ->maxLength(255)
                    ->required(),

                    Forms\Components\FileUpload::make('thumbnail')
                    ->required()
                    ->image(),
                ]),

                Fieldset::make('Additional')
                ->schema([
                    // ...

                    Forms\Components\Repeater::make('benefits')
                    ->relationship('benefits')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                        ->required(),
                    ]),

                    Forms\Components\Textarea::make('about')
                    ->required(),

                    Forms\Components\Select::make('is_popular')
                    ->options([
                        true => 'Popular',
                        false => 'Not Popular',
                    ])
                    ->required(),

                    Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
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
