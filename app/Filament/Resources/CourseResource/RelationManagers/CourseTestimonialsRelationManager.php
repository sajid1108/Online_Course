<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;

class TestimonialsRelationManager extends RelationManager {
    protected static string $relationship = 'testimonials'; // Sesuai dengan relasi di model Course

    public function form(Forms\Form $form): Forms\Form {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('job')->required(),
            Forms\Components\Textarea::make('testimonial')->required(),
            Forms\Components\FileUpload::make('photo')->image()->directory('testimonials'),
        ]);
    }

    public function table(Tables\Table $table): Tables\Table {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('job'),
            Tables\Columns\TextColumn::make('testimonial')->limit(50),
            Tables\Columns\ImageColumn::make('photo')->circular(),
        ]);
    }
}
