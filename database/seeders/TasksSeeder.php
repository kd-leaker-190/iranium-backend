<?php

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;
use Modules\FileUpload\Models\FileUpload;
use Modules\Task\Enum\TaskType;
use Modules\Task\Models\Task;
use Modules\MCQ\Models\MCQ;

class TasksSeeder extends Seeder
{
    static array $tasks = [];

    public function run(): void
    {
        // skip header
        array_shift(self::$tasks);

        $header = [
            'id',
            'action_id',
            'question',
            'o1',
            'o2',
            'o3',
            'o4',
            'answer',
            'score'
        ];

        foreach (self::$tasks as $row) {
            if ($row[0] === null) {
                continue;
            }

            $row = array_combine($header, $row);

            // Create MCQ model first
            $mcq = MCQ::create([
                'question' => $row['question'],
                'answer' => $row['answer'],
                'options' => [
                    [
                        'value' => 0,
                        'label' => $row['o1'],
                    ],
                    [
                        'value' => 1,
                        'label' => $row['o2'],
                    ],
                    [
                        'value' => 2,
                        'label' => $row['o3'],
                    ],
                    [
                        'value' => 3,
                        'label' => $row['o4'],
                    ],
                ]
            ]);

            Task::create([
                'action_id' => $row['action_id'],
                'taskable_type' => MCQ::class,
                'taskable_id' => $mcq->id,
                'type' => TaskType::MCQ->value,
                'score' => $row['score'],
                'duration' => 10, // minute
                'need_review' => false
            ]);
        }

        Action::get()->each(function (Action $action) {
            FileUpload::create([
                'description' => "فایل مورد نظر خود در رابطه با عملیات را آپلود کنید",
            ]);
            $fileUpload = FileUpload::latest()->firstOrFail();

            Task::create([
                'action_id' => $action->id,
                'taskable_type' => FileUpload::class,
                'taskable_id' => $fileUpload->id,
                'type' => TaskType::UploadFile->value,
                'score' => $action->score,
                'duration' => 10,
                'need_review' => false
            ]);
        });
    }
}
