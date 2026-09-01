<?php /** @noinspection PhpUndefinedMethodInspection */

namespace Database\Seeders;

use App\Models\Action;
use Illuminate\Database\Seeder;

class ActionSeeder extends Seeder
{
    static array $actions = [];

    public function run(): void
    {
        // skip header
        array_shift(self::$actions);

        $header = [
            'id',
            'name',
            'score',
            'region_id',
        ];

        foreach (self::$actions as $row) {
            $row = array_combine($header, $row);

            $id = $row['id'];

            $action = Action::create([
                'id' => $id,
                'name' => $row['name'],
                'score' => $row['score'],
                'region_id' => $row['region_id'],
            ]);

            $action->addMedia(public_path("action-icons/actionIcon$id.png"))
                ->preservingOriginal()
                ->toMediaCollection('icon');

            $action->addMedia(public_path("action-documents/boys/actionDocumentBoy$id.pdf"))
                ->preservingOriginal()
                ->toMediaCollection('attachment_boy');

            $action->addMedia(public_path("action-documents/girls/actionDocumentGirl$id.pdf"))
                ->preservingOriginal()
                ->toMediaCollection('attachment_girl');
        }
    }
}
