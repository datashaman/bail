<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use OpenAI\Laravel\Facades\OpenAI;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $documents = collect([
            [
                "title" => "The Importance of Artificial Intelligence in Modern Healthcare",
                "content" => "Artificial intelligence (AI) is revolutionizing modern healthcare by enabling more accurate diagnoses, personalized treatment plans, and efficient patient care management."
            ],
            [
                "title" => "Advancements in Renewable Energy Technologies",
                "content" => "Renewable energy technologies have seen significant advancements in recent years, including improvements in solar panels, wind turbines, and energy storage solutions."
            ],
            [
                "title" => "The Role of Blockchain in Supply Chain Management",
                "content" => "Blockchain technology is transforming supply chain management by enhancing transparency, traceability, and security across the entire supply chain ecosystem."
            ],
            [
                "title" => "Understanding Quantum Computing: Concepts and Applications",
                "content" => "Quantum computing utilizes the principles of quantum mechanics to perform complex computations at speeds unimaginable by classical computers, unlocking new possibilities in fields such as cryptography, optimization, and material science."
            ],
            [
                "title" => "The Impact of 5G Technology on Industry and Society",
                "content" => "5G technology promises ultra-fast internet speeds, low latency, and massive connectivity, fueling innovations in autonomous vehicles, smart cities, and augmented reality applications."
            ],
            [
                "title" => "Exploring the Potential of Virtual Reality in Education",
                "content" => "Virtual reality (VR) has the potential to revolutionize education by providing immersive learning experiences that engage students and enhance comprehension of complex concepts."
            ],
            [
                "title" => "The Rise of Artificial Intelligence in Financial Services",
                "content" => "Artificial intelligence is reshaping the financial services industry by automating processes, detecting fraudulent activities, and delivering personalized financial recommendations to customers."
            ],
            [
                "title" => "Ethical Considerations in the Development of Autonomous Vehicles",
                "content" => "The development of autonomous vehicles raises important ethical considerations related to safety, liability, privacy, and the impact on traditional transportation industries."
            ],
            [
                "title" => "The Future of Work: Adapting to Automation and AI",
                "content" => "As automation and artificial intelligence continue to advance, the future of work will require individuals and organizations to adapt by acquiring new skills, embracing remote work, and fostering innovation."
            ],
            [
                "title" => "Addressing Cybersecurity Challenges in the Digital Age",
                "content" => "With the increasing digitization of society, cybersecurity has become a critical concern, requiring robust strategies to protect against cyber threats, data breaches, and malicious attacks."
            ],
        ]);

        $result = OpenAI::embeddings()->create([
            'model' => config('openai.embedding_model'),
            'input' => $documents->pluck('content')->all(),
        ]);

        foreach ($documents as $index => $document) {
            Document::create([
                'title' => $document['title'],
                'content' => $document['content'],
                'meta' => [],
                'content_type' => 'text/plain',
                'embedding' => $result->embeddings[$index]->embedding,
                'hash' => md5(json_encode([
                    'content' => $document['content'],
                    'meta' => [],
                ])),
            ]);
        }
    }
}
