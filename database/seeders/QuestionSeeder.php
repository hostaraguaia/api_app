<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'question' => 'Quem é o atual Papa da Igreja Católica?',
                'difficulty' => 'easy',
                'answers' => [
                    ['answer' => 'Papa Francisco', 'is_correct' => true],
                    ['answer' => 'Papa Bento XVI', 'is_correct' => false],
                    ['answer' => 'Papa João Paulo II', 'is_correct' => false],
                    ['answer' => 'Papa Pio XII', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quantos sacramentos existem na Igreja Católica?',
                'difficulty' => 'easy',
                'answers' => [
                    ['answer' => '5', 'is_correct' => false],
                    ['answer' => '7', 'is_correct' => true],
                    ['answer' => '10', 'is_correct' => false],
                    ['answer' => '12', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual é o primeiro livro da Bíblia?',
                'difficulty' => 'easy',
                'answers' => [
                    ['answer' => 'Êxodo', 'is_correct' => false],
                    ['answer' => 'Gênesis', 'is_correct' => true],
                    ['answer' => 'Levítico', 'is_correct' => false],
                    ['answer' => 'Mateus', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual é o nome da mãe de Jesus?',
                'difficulty' => 'easy',
                'answers' => [
                    ['answer' => 'Maria', 'is_correct' => true],
                    ['answer' => 'Isabel', 'is_correct' => false],
                    ['answer' => 'Ana', 'is_correct' => false],
                    ['answer' => 'Madalena', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quantos mandamentos Deus entregou a Moisés?',
                'difficulty' => 'easy',
                'answers' => [
                    ['answer' => '5', 'is_correct' => false],
                    ['answer' => '7', 'is_correct' => false],
                    ['answer' => '10', 'is_correct' => true],
                    ['answer' => '12', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual sacramento marca a entrada na vida cristã?',
                'difficulty' => 'medium',
                'answers' => [
                    ['answer' => 'Batismo', 'is_correct' => true],
                    ['answer' => 'Confirmação', 'is_correct' => false],
                    ['answer' => 'Eucaristia', 'is_correct' => false],
                    ['answer' => 'Matrimônio', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quantos Evangelhos existem no Novo Testamento?',
                'difficulty' => 'medium',
                'answers' => [
                    ['answer' => '2', 'is_correct' => false],
                    ['answer' => '3', 'is_correct' => false],
                    ['answer' => '4', 'is_correct' => true],
                    ['answer' => '5', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual é o significado da palavra "Eucaristia"?',
                'difficulty' => 'medium',
                'answers' => [
                    ['answer' => 'Ação de graças', 'is_correct' => true],
                    ['answer' => 'Perdão', 'is_correct' => false],
                    ['answer' => 'Santificação', 'is_correct' => false],
                    ['answer' => 'Comunhão', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quem foi o primeiro Papa da Igreja Católica?',
                'difficulty' => 'medium',
                'answers' => [
                    ['answer' => 'São Paulo', 'is_correct' => false],
                    ['answer' => 'São Pedro', 'is_correct' => true],
                    ['answer' => 'São João', 'is_correct' => false],
                    ['answer' => 'São Tiago', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual é o nome da oração que Jesus ensinou aos seus discípulos?',
                'difficulty' => 'medium',
                'answers' => [
                    ['answer' => 'Ave Maria', 'is_correct' => false],
                    ['answer' => 'Pai Nosso', 'is_correct' => true],
                    ['answer' => 'Credo', 'is_correct' => false],
                    ['answer' => 'Glória', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual Concílio estabeleceu o Credo Niceno?',
                'difficulty' => 'hard',
                'answers' => [
                    ['answer' => 'Concílio de Niceia', 'is_correct' => true],
                    ['answer' => 'Concílio de Trento', 'is_correct' => false],
                    ['answer' => 'Concílio Vaticano I', 'is_correct' => false],
                    ['answer' => 'Concílio Vaticano II', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quantos livros tem a Bíblia Católica?',
                'difficulty' => 'hard',
                'answers' => [
                    ['answer' => '66', 'is_correct' => false],
                    ['answer' => '73', 'is_correct' => true],
                    ['answer' => '77', 'is_correct' => false],
                    ['answer' => '80', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual foi o primeiro milagre de Jesus?',
                'difficulty' => 'hard',
                'answers' => [
                    ['answer' => 'Multiplicação dos pães', 'is_correct' => false],
                    ['answer' => 'Bodas de Caná', 'is_correct' => true],
                    ['answer' => 'Cura do cego', 'is_correct' => false],
                    ['answer' => 'Ressurreição de Lázaro', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Qual é o nome do anjo que anunciou a Maria que ela seria a mãe de Jesus?',
                'difficulty' => 'hard',
                'answers' => [
                    ['answer' => 'São Miguel', 'is_correct' => false],
                    ['answer' => 'São Rafael', 'is_correct' => false],
                    ['answer' => 'São Gabriel', 'is_correct' => true],
                    ['answer' => 'São Uriel', 'is_correct' => false],
                ]
            ],
            [
                'question' => 'Quantas bem-aventuranças Jesus proclamou no Sermão da Montanha?',
                'difficulty' => 'hard',
                'answers' => [
                    ['answer' => '7', 'is_correct' => false],
                    ['answer' => '8', 'is_correct' => true],
                    ['answer' => '10', 'is_correct' => false],
                    ['answer' => '12', 'is_correct' => false],
                ]
            ],
        ];

        foreach ($questions as $questionData) {
            $question = Question::create([
                'question' => $questionData['question'],
                'difficulty' => $questionData['difficulty'],
                'is_active' => true,
            ]);

            foreach ($questionData['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer' => $answerData['answer'],
                    'is_correct' => $answerData['is_correct'],
                ]);
            }
        }
    }
}
