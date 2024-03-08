<?php

namespace Database\Seeders;

use App\Models\MessageType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MessageType::query()->create([
            'name' => 'text',
            'description' => 'Tipo de mensagem texto',
            'media_type' => "text"
        ]);

        MessageType::query()->create([
            'name' => 'audio',
            'description' => 'Tipo de mensagem áudio',
            'media_type' => "audio",
            'active' => 0
        ]);

        MessageType::query()->create([
            'name' => 'image',
            'description' => 'Tipo de mensagem imagem',
            'media_type' => "image"
        ]);

        MessageType::query()->create([
            'name' => 'video',
            'description' => 'Tipo de mensagem video',
            'media_type' => "video"
        ]);

        MessageType::query()->create([
            'name' => 'document',
            'description' => 'Tipo de mensagem documento',
            'media_type' => "document",
            'active' => 0
        ]);

        MessageType::query()->create([
            'name' => 'sticky',
            'description' => 'Tipo de mensagem figurinha',
            'media_type' => "image"
        ]);
    }
}
