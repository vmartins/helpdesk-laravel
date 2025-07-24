<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Filament\Resources\TicketResource;
use App\Models\Comment;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\TicketSettings;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Livewire\Component as Livewire;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Comments');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    protected function canCreate(): bool
    {
        return Gate::allows('create', [$this->getTable()->getModel(), $this->ownerRecord]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\RichEditor::make('comment')
                        ->translateLabel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('attachments')
                        ->translateLabel()
                        ->directory('comment-attachments/' . date('m-y'))
                        ->maxSize(2000)
                        ->downloadable(),
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modelLabel(__('Comment'))
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('user.name')
                            ->translateLabel()
                            ->weight('bold')
                            ->grow(false),
                        TextColumn::make('created_at')
                            ->translateLabel()
                            ->dateTime(app(GeneralSettings::class)->datetime_format)
                            ->color('secondary'),
                    ]),
                    TextColumn::make('comment')
                        ->wrap()
                        ->html(),
                ]),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    })
                    ->after(function (Livewire $livewire) {
                        $ticket = $livewire->ownerRecord;

                        if (auth()->user()->hasAnyRole(['Admin Unit', 'Staf Unit'])) {
                            $receiver = $ticket->owner;
                        } else {
                            $receiver = User::whereHas(
                                'roles',
                                function ($q) {
                                    $q->where('name', 'Admin Unit')
                                        ->orWhere('name', 'Staf Unit');
                                },
                            )->get();
                        }

                        Notification::make()
                            ->title(__('There are new comments on your ticket'))
                            ->actions([
                                Action::make(__('Show'))
                                    ->url(TicketResource::getUrl('view', ['record' => $ticket->id])),
                            ])
                            ->sendToDatabase($receiver);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('attachment')
                    ->translateLabel()
                    ->action(function ($record) {
                        return response()->download('storage/' . $record->attachments);
                    })
                    ->hidden(fn ($record) => $record->attachments == ''),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->paginated(false);
    }
}
