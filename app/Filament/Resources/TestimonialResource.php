<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\CourseTestimonial;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;

class TestimonialResource extends Resource {
    protected static ?string $model = CourseTestimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat';

    public static function form(Forms\Form $form): Forms\Form {
        return $form->schema([
            Forms\Components\Select::make('course_id')
                ->relationship('course', 'title')
                ->required(),

            Forms\Components\TextInput::make('name')
                ->required(),

            Forms\Components\TextInput::make('job')
                ->required(),

            Forms\Components\Textarea::make('testimonial')
                ->required(),

            Forms\Components\FileUpload::make('photo')
                ->image()
                ->directory('testimonials'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('course.title')->label('Course'),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('job'),
            Tables\Columns\TextColumn::make('testimonial')->limit(50),
            Tables\Columns\ImageColumn::make('photo')->circular(),
        ])->filters([]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
