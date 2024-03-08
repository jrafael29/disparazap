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
            'description' => 'Mensagem de texto',
            'media_type' => "text"
        ]);

        MessageType::query()->create([
            'name' => 'audio',
            'description' => 'Áudio',
            'media_type' => "audio",
            'active' => 0
        ]);

        MessageType::query()->create([
            'name' => 'image',
            'description' => 'Imagem',
            'media_type' => "image"
        ]);

        MessageType::query()->create([
            'name' => 'video',
            'description' => 'Vídeo',
            'media_type' => "video"
        ]);

        MessageType::query()->create([
            'name' => 'document',
            'description' => 'Documento',
            'media_type' => "document",
            'active' => 0
        ]);

        MessageType::query()->create([
            'name' => 'sticky',
            'description' => 'Figurinha',
            'media_type' => "image"
        ]);
    }
}
