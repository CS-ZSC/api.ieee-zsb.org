<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $news = News::firstOrCreate(
            ['title' => 'Robotiva Finals Triumph'],
            [
                'description' => 'Our IEEE RAS chapter sent five teams to the prestigious Robotiva competition, and all five teams made it to the finals! With SOUL winning 1st place and Intellibots securing 2nd, RAS once again proved that dedication, teamwork, and relentless learning lead to excellence. This victory isn\'t just a trophy—it\'s a statement of what we stand for.',
                'date_created' => '2025-02-27',
                'author' => 'Marwan Tamer',
                'home_item' => true,
                'tags' => ['RAS', 'Robotiva', 'Competitions'],
                'main_photo' => '/News/robotiva/soul.webp',
            ]
        );

        $sections = [
            [
                'heading' => 'A Competition That Put Us to the Test',
                'descriptions' => [
                    'Robotiva wasn\'t just any competition—it was a proving ground for creativity, engineering skill, and realworld problem solving. The challenge tasked participants with building a fully functional robot that could handle three distinct missions: line following, obstacle avoidance, and a pick-and-place task using a robotic arm. Each functionality required tight integration between hardware and software, and every team had to deal with sensor calibration issues, motor control precision, path-planning logic, and gripper mechanics.',
                    'From electrical noise on sensor inputs to debugging edge cases in autonomous logic, the teams faced wave after wave of technical hurdles. But as always—this is RAS. We don\'t back down from problems; we grow stronger because of them. Each team worked tirelessly, sharing designs, testing day and night, and fine-tuning their code to meet competition standards. The result? Five finalist teams from one chapter—a statistic that speaks volumes.',
                ],
            ],
            [
                'heading' => 'Meet the Winners: SOUL and Intellibots',
                'descriptions' => [
                    'Among the five RAS finalist teams, two rose above the rest. SOUL, led by a core of experienced members, demonstrated flawless execution across all three challenges. Their robot maintained steady performance with highly optimized PID control and an impressively stable robotic arm mechanism. Their design was not just technically superior, but also elegant—earning praise from both judges and spectators alike.',
                    'Intellibots, on the other hand, stood out for their advanced path-planning algorithms and robust error-handling capabilities. Their robot showed remarkable resilience under pressure, with quick adaptations mid-run that reflected the team\'s deep understanding of both hardware constraints and real-time software logic. These two victories mark a historical achievement for our chapter and set a new benchmark for future competitions.',
                ],
                'photo' => '/News/robotiva/soul.webp',
                'photo_description' => 'Soul Team',
            ],
            [
                'heading' => 'More Than Just Winners',
                'descriptions' => [
                    'While the podium finishes are worth celebrating, the true success lies in the journey. Every RAS participant walked away with real engineering experience—learning not just how to build robots, but how to work under pressure, debug collaboratively, and face failure with determination.',
                    'From late-night brainstorming sessions to emergency soldering repairs, this experience became a high-impact learning ground. These lessons were shared throughout the chapter—transforming mistakes into mentorship and individual struggles into collective growth. The effects were clear during later events like The Rockies Final Competition, where the influence of Robotiva showed in the confidence, preparedness, and innovation of our members.',
                ],
                'photo' => '/News/robotiva/intellibots.webp',
                'photo_description' => 'Intellibots Team',
            ],
        ];

        foreach ($sections as $index => $section) {
            $news->sections()->firstOrCreate(
                ['heading' => $section['heading']],
                [
                    'descriptions' => $section['descriptions'],
                    'photo' => $section['photo'] ?? null,
                    'photo_description' => $section['photo_description'] ?? null,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
