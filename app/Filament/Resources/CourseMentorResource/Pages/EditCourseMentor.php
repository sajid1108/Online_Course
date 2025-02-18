<?php

namespace App\Filament\Resources\CourseMentorResource\Pages;

use App\Filament\Resources\CourseMentorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourseMentor extends EditRecord
{
    protected static string $resource = CourseMentorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
