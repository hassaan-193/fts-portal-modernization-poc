<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlterFilePathRawSql extends Migration
{
    public function up()
    {
      DB::statement("
      UPDATE `projectreports`
      SET `file_path` = '[]'
      WHERE `file_path` IS NULL
        OR JSON_VALID(`file_path`) = 0
      ");

      DB::statement("
      ALTER TABLE `projectreports`
      MODIFY `file_path` JSON NULL
      ");
    }

    public function down()
    {
        DB::statement("
          ALTER TABLE `projectreports`
          MODIFY `file_path` VARCHAR(191) NULL
        ");    }
}
