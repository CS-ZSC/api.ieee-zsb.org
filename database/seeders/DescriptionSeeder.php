<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Description;
use Illuminate\Database\Seeder;

class DescriptionSeeder extends Seeder
{
    public function run(): void
    {
        $descriptions = [
            'IEEE' => [
                'about' => 'IEEE Zewail City Student Branch is a community of engineering students dedicated to advancing technology for humanity.',
                'vision' => 'To be the leading student branch fostering innovation and technical excellence.',
                'mission' => 'Empowering students through technical workshops, networking events, and professional development opportunities.',
            ],
            'CS' => [
                'about' => 'The Computer Society chapter focuses on computing, software engineering, and information technology.',
                'vision' => 'To build a community of skilled software engineers and computer scientists.',
                'mission' => 'Providing hands-on experience in software development, AI, cybersecurity, and data science.',
            ],
            'PES' => [
                'about' => 'The Power & Energy Society chapter is dedicated to power engineering and sustainable energy solutions.',
                'vision' => 'To lead the transition towards smart and sustainable energy systems.',
                'mission' => 'Educating students about power systems, automation, and emerging energy technologies.',
            ],
            'RAS' => [
                'about' => 'The Robotics and Automation Society chapter covers robotics, embedded systems, and automation.',
                'vision' => 'To inspire the next generation of robotics engineers and innovators.',
                'mission' => 'Hands-on learning in mechanical design, embedded systems, PCB design, and ROS.',
            ],
            'WIE' => [
                'about' => 'Women in Engineering is dedicated to promoting women in technical disciplines.',
                'vision' => 'To create an inclusive engineering community where women thrive and lead.',
                'mission' => 'Supporting and empowering women in engineering through mentorship, events, and advocacy.',
            ],
        ];

        foreach ($descriptions as $shortName => $data) {
            $chapter = Chapter::where('short_name', $shortName)->first();
            if ($chapter) {
                Description::firstOrCreate(
                    ['chapter_id' => $chapter->id],
                    $data + ['chapter_id' => $chapter->id]
                );
            }
        }
    }
}
