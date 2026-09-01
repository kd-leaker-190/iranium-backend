<?php

namespace App\Filament\Resources;

use App\Enums\ActionStatus;
use App\Filament\Resources\ActionTeamResource\Pages;
use App\Filament\Resources\ActionTeamResource\RelationManagers;
use App\Models\ActionTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action as TableAction;
use Modules\FileUpload\Models\FileUploadTeam;
use Modules\Task\Models\TaskTeam;

class ActionTeamResource extends Resource
{
    protected static ?string $model = ActionTeam::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'عملیات‌های انجام شده';
    protected static ?string $pluralLabel = 'عملیات‌های انجام شده';
    protected static ?string $modelLabel = 'عملیات انجام شده';
    protected static ?string $navigationGroup = 'عملیات‌ها';
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team.name')
                    ->label('تیم')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('action.name')
                    ->label('نام عملیات')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state?->getLabel() ?? $state)
                    ->color(fn($state) => match ($state) {
                        ActionStatus::Pending => 'warning',
                        ActionStatus::Completed => 'primary',
                        ActionStatus::Timeout => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ انجام عملیات')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                TableAction::make('show_task_uploads')
                    ->label('فایل‌های عملیات')
                    ->icon('heroicon-o-paper-clip')
                    ->modalHeading(fn($record) => "فایل‌های آپلود شده برای عملیات: {$record->action?->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('بستن')
                    ->modalWidth('5xl')
                    ->modalContent(function ($record) {
                        $taskTeams = TaskTeam::query()
                            ->where('team_id', $record->team_id)
                            ->whereHas('task', fn($q) => $q->where('action_id', $record->action_id))
                            ->with(['team', 'task.action', 'task.taskable']) // team برای اسم فایل
                            ->latest()
                            ->get();

                        // همه file_upload_id هایی که احتمالاً فایل دارند
                        $fileUploadIds = $taskTeams
                            ->pluck('task.taskable_id')
                            ->filter()
                            ->unique()
                            ->values();

                        // یکبار همه FileUploadTeam های مربوط به این تیم را بگیر (به جای N+1)
                        $fileUploadTeams = FileUploadTeam::query()
                            ->where('team_id', $record->team_id)
                            ->whereIn('file_upload_id', $fileUploadIds)
                            ->get()
                            ->keyBy('file_upload_id');

                        $items = $taskTeams->map(function (TaskTeam $taskTeam) use ($fileUploadTeams) {
                            $fileUploadId = $taskTeam->task?->taskable_id;

                            $fileUploadTeam = $fileUploadId ? $fileUploadTeams->get($fileUploadId) : null;

                            /** @var FileUploadTeam|null $fileUploadTeam */
                            $media = $fileUploadTeam?->getFirstMedia('file');

                            return [
                                'task_title' => $taskTeam->task_title,
                                'task_type' => $taskTeam->task?->type?->value ?? '-',
                                'done_at' => $taskTeam->created_at,
                                'has_file' => (bool) $media,
                                'download_name' => $media ? $taskTeam->getDownloadFileName($media) : null,
                                'file_url' => $media ? route('api.admin.task-teams.media.download', [
                                    'taskTeam' => $taskTeam->id,
                                    'media' => $media->id,
                                ]) : null,
                            ];
                        })->sortByDesc('has_file')->values();

                        return view('filament.modals.action-team-task-uploads', [
                            'record' => $record,
                            'items' => $items,
                        ]);
                    })
                    ->visible(function ($record) {
                        return TaskTeam::query()
                            ->where('team_id', $record->team_id)
                            ->whereHas('task', fn($q) => $q->where('action_id', $record->action_id))
                            ->whereHas('task', fn($q) => $q->whereNotNull('taskable_id'))
                            ->exists();
                    }),
            ])
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
            'index' => Pages\ListActionTeams::route('/'),
        ];
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
}
