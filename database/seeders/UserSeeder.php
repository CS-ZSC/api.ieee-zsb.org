<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $chapters = Chapter::all()->keyBy('short_name');
        $committees = Committee::all()->keyBy('name');
        $tracks = Track::all();
        $positions = Position::all()->keyBy('name');

        $defaultPassword = Hash::make('12345678');

        $users = [
            // ===== IEEE Chapter - Executive Board =====
            [
                'name' => 'Ahmed Raiyah',
                'email' => 'ahmed.raiyah@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ahmed-raiyah',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Chairperson',
            ],
            [
                'name' => 'Ahmed Medhat',
                'email' => 'ahmed.medhat@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ahmed-medhat-212847271',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Technical Vice Chairperson',
            ],
            [
                'name' => 'Mohamed Abdalaziz',
                'email' => 'mohamed.abdalaziz@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-abdalaziz-hussien/',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Managerial Vice Chairperson',
            ],
            [
                'name' => 'Mohamed Emad',
                'email' => 'mohamed.elsawy@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/hulxv',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Webmaster',
            ],
            [
                'name' => 'Engy Mohamed',
                'email' => 'engy.mohamed@ieee-zsb.org',
                'linkedin' => 'https://linkedin.com/in/engy-mohammed-54706a336',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Secretary',
            ],
            [
                'name' => 'Ahmed Elnaggar',
                'email' => 'ahmed.elnaggar@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ahmedelnaggarr',
                'group_type' => 'chapter',
                'group_name' => 'IEEE',
                'position' => 'Treasurer',
            ],

            // ===== CS Chapter =====
            [
                'name' => 'Ahmed Elsherbiny',
                'email' => 'ahmed-elsherbiny@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ahmedelsherbiny74',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'position' => 'Chairperson',
            ],
            [
                'name' => 'Asmaa Mohamed Saleh',
                'email' => 'asmaa.saleh@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/asmaa-saleh185',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Omar Salama',
                'email' => 'omar.salama@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/omar-salama-0720b22a7',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Manar Ahmed Mohamed',
                'email' => 'eng.manar.ahmed20@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/manar-ahmed20',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Frontend',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mostafa Mahmoud Elshahat',
                'email' => 'mostafa.mahmoud.elshahat1@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mostafaelshahat',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Artificial Intelligence',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Marwan Hossam',
                'email' => 'marwanhossam630@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/marwan-hossam-7240a9302',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Cyber Security',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Sohaila Samy Galal',
                'email' => 'sohailasamy59@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/sohaila-samy-galal',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Data Science',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mohamed Wael',
                'email' => 'waelm7860@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mhmdwaelmhdi',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Mobile',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mohamed Abbas',
                'email' => 'mohamedadel96e@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-adel96e',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Backend',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Ayman Yasser',
                'email' => 'ayman.yasser227@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/ayman-yasser-45b6402a7/',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Artificial Intelligence',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Shahd Mahmoud',
                'email' => 'eng.shahda@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/shahd-mahmoud0/',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Cyber Security',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Abdullah Awadallah',
                'email' => 'abdallahhamada2103@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/abdallah-awadallah-4331a7298/',
                'group_type' => 'chapter',
                'group_name' => 'CS',
                'track_name' => 'Backend',
                'position' => ['Dev', 'Track Vice-Lead'],
            ],

            // ===== PES Chapter =====
            [
                'name' => 'Eslam Mahmoud',
                'email' => 'eslam.mahmoud@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/eslam-mahmoud-magdy',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'position' => 'Chairperson',
            ],
            [
                'name' => 'Mohamed Shaban Abdelhalim',
                'email' => 'mohamedshabaan2453@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-shaban-2660a4277',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Ibrahim Mohamed Askar',
                'email' => 'ibrahim.askar@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ibrahim-askar-66b436254',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Abdelrahman Khedr',
                'email' => 'abdelrahman.yasser@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/abdelrahman-yasser-883077279',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Samira Mohammed Abdelaaty',
                'email' => 'smiramohammed6123@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/samira-mohammed-0912852a2',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Basic Automation',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Mohammad Abowarda',
                'email' => 'mohammadabowarda.eng@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohammad-abowarda',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Smart Home',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mina Mahfouz',
                'email' => 'minamahfouz22@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mina-mahfouz-9b9875286',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'E-Mobility',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mostafa Ahmed',
                'email' => 'mostafaahmed5332442@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mostafa-ahmed-m',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Advanced Automation',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Eslam Mahmoud Abuelela',
                'email' => 'eslamabuelela111@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/eslam-abuelela',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Distribution',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Kyrillos Nabil Ghaly',
                'email' => 'Kyrillos.Nabil.Ghaly@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/kyrillos-nabil-288421312',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Basic Automation',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mohamed Abdelrahman Akrm',
                'email' => 'akrm73011@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-akrm-695145335',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'E-Mobility',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Ziad Mohamed Saeed',
                'email' => 'ziadmohamedsaeed00@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/ziad-saeed-011b92335',
                'group_type' => 'chapter',
                'group_name' => 'PES',
                'track_name' => 'Distribution',
                'position' => 'Track Lead',
            ],

            // ===== RAS Chapter =====
            [
                'name' => 'Abdelrahman Elghandour',
                'email' => 'abdelrahman.elghandour@ieee-zsb.org',
                'linkedin' => null,
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'position' => 'Chairperson',
            ],
            [
                'name' => 'Hamdi Emad',
                'email' => 'hamdyemadelgohary04@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/hamdi-algohary-9962b5335',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Norhan Yasser Khidr',
                'email' => 'norhankhidr12@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/norhan-khidr-7463392a6',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'position' => 'Vice Chairperson',
            ],
            [
                'name' => 'Abdelrahman Abdellateef',
                'email' => 'rahmanmlateef@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/abdelrahmanabdellateef',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Mechanical Design',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Ahmed Ibrahim Ali',
                'email' => 'ahmed.ibrahem@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ahmed-ibrahim-344383300',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'PCB Design',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Alaa Ahmed Abdelhay',
                'email' => 'alaaabdelhay65@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/a-laa-abdelhay-16a909239',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Embedded Systems',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Jesy Ahmed',
                'email' => 'jesyahmedabdelaal@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/jasmine-ahmed-892226335',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Embedded Systems',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Mohamed Akram Abo Elftooh',
                'email' => 'Mohamedakram0900@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-akram-',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Mechanical Design',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Hassan Emad Zein',
                'email' => 'hassanemad.eng@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/hassan-e-zein',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Mechanical Design',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Nadine Haytham',
                'email' => 'nadine.e399@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/nadine-haytham-85044b318',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'PCB Design',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Nadeen Elhady',
                'email' => 'nadeenelhady300@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/nadeen-elhady-714538298',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'ROS',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Ahmed Hisham Abdelfattah',
                'email' => 'ahmed.hisham123666@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/ahmedhishambu',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'IC Design',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Awwab Khalil',
                'email' => 'awwab.khalil1425@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/awwab-khalil',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'ROS',
                'position' => 'Track Lead',
            ],
            [
                'name' => 'Mariam Adel Abdelazim',
                'email' => 'mariamadel4910@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mariam-adel-37a722320',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'Embedded Systems',
                'position' => 'Track Vice-Lead',
            ],
            [
                'name' => 'Mohammed Taher',
                'email' => 'motaher20004@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohammed-taher-halawaty2004/',
                'group_type' => 'chapter',
                'group_name' => 'RAS',
                'track_name' => 'IC Design',
                'position' => 'Track Lead',
            ],

            // ===== WIE Chapter =====
            [
                'name' => 'Aida Abdelazez',
                'email' => 'aida.abdelazez@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/aida-abdelazez-b54106330',
                'group_type' => 'chapter',
                'group_name' => 'WIE',
                'position' => 'Lead',
            ],
            [
                'name' => 'Hager Salah Mohamed Ismail',
                'email' => 'slahhager852@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/hager-ismail-',
                'group_type' => 'chapter',
                'group_name' => 'WIE',
                'position' => 'Vice Lead',
            ],

            // ===== Ambassadors Committee =====
            [
                'name' => 'Beshoy Seleman',
                'email' => 'beshoy.seleman@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/beshoy-seleman',
                'group_type' => 'committee',
                'group_name' => 'Ambassadors',
                'position' => 'Lead',
            ],
            [
                'name' => 'Hanem Reda Attia Elghamry',
                'email' => 'hanem.reda@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/hanem-elghamry',
                'group_type' => 'committee',
                'group_name' => 'Ambassadors',
                'position' => 'Vice Lead',
            ],

            // ===== Business Development Committee =====
            [
                'name' => 'Mohamed Ahmed Othman',
                'email' => 'mohamed.othman@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/mohamed--othman',
                'group_type' => 'committee',
                'group_name' => 'Business Development',
                'position' => 'Lead',
            ],
            [
                'name' => 'Mai Mahmoud Mohamed',
                'email' => 'maim61366@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mai-mahmoud-362b602ba',
                'group_type' => 'committee',
                'group_name' => 'Business Development',
                'position' => 'Vice Lead',
            ],

            // ===== Multimedia Committee =====
            [
                'name' => 'Shahd Moatz',
                'email' => 'shahdmoatz@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/shahdmoatz',
                'group_type' => 'committee',
                'group_name' => 'Multimedia',
                'position' => 'Lead',
            ],
            [
                'name' => 'Ziad Ashraf',
                'email' => 'ziad.abdelwahed@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ziad-ashraf-3a77412b8',
                'group_type' => 'committee',
                'group_name' => 'Multimedia',
                'position' => 'Vice Lead',
            ],
            [
                'name' => 'Mohamed Mohsen',
                'email' => 'mohsn9165@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-mohsen-29a9392b1',
                'group_type' => 'committee',
                'group_name' => 'Multimedia',
                'position' => 'Vice Lead',
            ],

            // ===== Operations Committee =====
            [
                'name' => 'Ziad Awad',
                'email' => 'ziad.awad@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/ziadawad',
                'group_type' => 'committee',
                'group_name' => 'Operations',
                'position' => 'Lead',
            ],
            [
                'name' => 'Youssef Ebrahim',
                'email' => 'youssefebrahim299@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/youssef-ebrahim01',
                'group_type' => 'committee',
                'group_name' => 'Operations',
                'position' => 'Vice Lead',
            ],
            [
                'name' => 'Maximus Helmy Nashed Kamel',
                'email' => 'maxsimoushelmy@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/maximus-helmy',
                'group_type' => 'committee',
                'group_name' => 'Operations',
                'position' => 'Vice Lead',
            ],

            // ===== Talent & Tech Committee =====
            [
                'name' => 'Mahmoud Said',
                'email' => 'mahmoud.said@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/0xcode7/',
                'group_type' => 'committee',
                'group_name' => 'Talent & Tech',
                'position' => 'Lead',
            ],
            [
                'name' => 'Yousef Mokhles Mostafa',
                'email' => 'yousef.mokhles@ieee-zsb.org',
                'linkedin' => 'https://www.linkedin.com/in/yousef-mokhles-3b2966224',
                'group_type' => 'committee',
                'group_name' => 'Talent & Tech',
                'position' => 'Vice Lead',
            ],
            [
                'name' => 'Nada Gamal Eldek',
                'email' => 'nadagamal.sch@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/nada-gamal-569417284',
                'group_type' => 'committee',
                'group_name' => 'Talent & Tech',
                'position' => 'Vice Lead',
            ],

            // ===== Marketing Committee =====
            [
                'name' => 'Moaz Mohamed Elsayed Fawzy',
                'email' => 'muazmmd9@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/moaz-mohamed-fawzy',
                'group_type' => 'committee',
                'group_name' => 'Marketing',
                'position' => 'Lead',
            ],
            [
                'name' => 'Mohamed Elsharkawy Galhoum',
                'email' => 'mohamedgalhoum155@gmail.com',
                'linkedin' => 'https://www.linkedin.com/in/mohamed-galhoum-97721b255/',
                'group_type' => 'committee',
                'group_name' => 'Marketing',
                'position' => 'Vice Lead',
            ],
        ];

        foreach ($users as $userData) {
            // Resolve group
            if ($userData['group_type'] === 'chapter') {
                $group = $chapters[$userData['group_name']];
            } else {
                $group = $committees[$userData['group_name']];
            }

            // Resolve track
            $trackId = null;
            if (!empty($userData['track_name'])) {
                $track = $tracks->where('name', $userData['track_name'])
                    ->where('chapter_id', $group->id)
                    ->first();
                $trackId = $track?->id;
            }

            // Create user
            $user = $group->users()->firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $defaultPassword,
                    'linkedin' => $userData['linkedin'],
                    'track_id' => $trackId,
                    'email_verified_at' => now(),
                ]
            );

            // Attach positions (handle multiple correctly)
            $userPositions = is_array($userData['position']) ? $userData['position'] : [$userData['position']];
            $positionIds = [];
            foreach ($userPositions as $positionName) {
                $position = $positions[$positionName] ?? null;
                if ($position) { // Only check if position exists
                    $positionIds[] = $position->id;
                }
            }
            if (!empty($positionIds)) {
                $user->positions()->syncWithoutDetaching($positionIds);
            }
        }
    }
}
