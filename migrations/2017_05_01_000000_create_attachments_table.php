<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('model_id')->unsigned();
            // max length is max filename length
            $table->string('model_type', 255);

            $table->integer('user_id')->unsigned()->nullable();

            // sha512 produces fixed length value
            $table->char('sha512', 128);

            // filename max length: https://serverfault.com/a/9548, https://stackoverflow.com/a/265782
            $table->string('filename', 255);

            // mime_type max length: https://stackoverflow.com/a/1849792
            $table->string('mime_type', 255);

            // unsigned integer max size is 4G
            $table->integer('size')->unsigned();

            $table->string('path', 255);

            $table->integer('downloads')->unsigned()->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['deleted_at', 'model_type', 'model_id', 'sha512', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
}
