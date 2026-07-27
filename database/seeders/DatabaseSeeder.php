<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@jobfinder.test',
            'password' => Hash::make('Admin@12345'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $employers = collect([
            [
                'name' => 'Nexora Careers',
                'email' => 'employer@jobfinder.test',
                'company_name' => 'Nexora Technologies',
                'phone' => '0771234567',
                'bio' => 'A Colombo based product engineering company hiring Laravel, QA, and support talent.',
            ],
            [
                'name' => 'Ceylon Retail HR',
                'email' => 'retail@jobfinder.test',
                'company_name' => 'Ceylon Retail Group',
                'phone' => '0714455667',
                'bio' => 'Islandwide retail employer with opportunities in sales, stores, finance, and operations.',
            ],
            [
                'name' => 'LakBank Talent',
                'email' => 'banking@jobfinder.test',
                'company_name' => 'LakBank Finance',
                'phone' => '0752223344',
                'bio' => 'Financial services team recruiting trainees, analysts, and customer service professionals.',
            ],
            [
                'name' => 'Bright Ads People',
                'email' => 'marketing@jobfinder.test',
                'company_name' => 'Bright Ads Digital',
                'phone' => '0769988776',
                'bio' => 'Digital marketing agency working with Sri Lankan brands and export businesses.',
            ],
        ])->map(fn (array $data) => User::create(array_merge($data, [
            'password' => Hash::make('Employer@12345'),
            'role' => 'employer',
            'status' => 'active',
            'employer_verified_at' => now(),
        ])));

        User::create([
            'name' => 'Sample Seeker',
            'email' => 'seeker@jobfinder.test',
            'password' => Hash::make('Seeker@12345'),
            'role' => 'job_seeker',
            'status' => 'active',
            'phone' => '0777654321',
        ]);

        $jobs = [
            [
                'employer_id' => $employers[0]->id,
                'title' => 'Laravel Developer',
                'main_category' => 'IT',
                'sub_category' => 'Software Engineering',
                'location' => 'Colombo',
                'type' => 'Full-time',
                'salary' => 'Rs. 180,000 - 300,000',
                'description' => 'Build Laravel dashboards, APIs, and admin workflows for local business clients.',
                'requirements' => 'Laravel, PHP, MySQL, Git, REST APIs, and practical debugging skills.',
                'responsibilities' => 'Develop features, review code, improve database queries, and ship reliable releases.',
                'deadline' => now()->addMonth()->toDateString(),
            ],
            [
                'employer_id' => $employers[2]->id,
                'title' => 'Banking Trainee',
                'main_category' => 'Banking',
                'sub_category' => 'Trainee',
                'location' => 'Colombo',
                'type' => 'Full-time',
                'salary' => 'Rs. 75,000 - 95,000',
                'description' => 'Join a structured banking trainee programme with branch and operations exposure.',
                'requirements' => 'Passed A/L or diploma, strong communication, numeracy, and customer handling skills.',
                'responsibilities' => 'Assist customers, process documents, support branch operations, and learn compliance basics.',
                'deadline' => now()->addWeeks(4)->toDateString(),
            ],
            [
                'employer_id' => $employers[1]->id,
                'title' => 'Accounts Assistant',
                'main_category' => 'Finance',
                'sub_category' => 'Accounting',
                'location' => 'Gampaha',
                'type' => 'Full-time',
                'salary' => 'Rs. 85,000 - 130,000',
                'description' => 'Support daily accounting work for a growing retail finance department.',
                'requirements' => 'AAT part qualified, Excel skills, basic VAT knowledge, and attention to detail.',
                'responsibilities' => 'Handle invoices, petty cash, supplier payments, reconciliations, and monthly reports.',
                'deadline' => now()->addWeeks(5)->toDateString(),
            ],
            [
                'employer_id' => $employers[3]->id,
                'title' => 'Digital Marketing Executive',
                'main_category' => 'Marketing',
                'sub_category' => 'Digital Marketing',
                'location' => 'Remote',
                'type' => 'Hybrid',
                'salary' => 'Rs. 120,000 - 180,000',
                'description' => 'Plan and run social media, paid campaigns, and content calendars for Sri Lankan brands.',
                'requirements' => 'Meta Ads, Google Ads basics, Sinhala/English copywriting, and campaign reporting.',
                'responsibilities' => 'Create campaign plans, coordinate creatives, monitor budgets, and report performance.',
                'deadline' => now()->addWeeks(3)->toDateString(),
            ],
            [
                'employer_id' => $employers[0]->id,
                'title' => 'QA Engineer',
                'main_category' => 'IT',
                'sub_category' => 'Quality Assurance',
                'location' => 'Kandy',
                'type' => 'Full-time',
                'salary' => 'Rs. 140,000 - 220,000',
                'description' => 'Test web applications, write test cases, and help product teams release confidently.',
                'requirements' => 'Manual testing, API testing, SQL basics, Jira, and clear bug reporting.',
                'responsibilities' => 'Prepare test plans, verify fixes, run regression checks, and improve QA documentation.',
                'deadline' => now()->addWeeks(6)->toDateString(),
            ],
            [
                'employer_id' => $employers[1]->id,
                'title' => 'Sales Executive',
                'main_category' => 'Sales',
                'sub_category' => 'Field Sales',
                'location' => 'Kurunegala',
                'type' => 'Full-time',
                'salary' => 'Rs. 90,000 + commission',
                'description' => 'Grow dealer relationships and visit retail partners across the North Western Province.',
                'requirements' => 'Bike license, Sinhala communication, sales confidence, and basic reporting skills.',
                'responsibilities' => 'Visit customers, collect orders, follow up payments, and update daily sales reports.',
                'deadline' => now()->addWeeks(2)->toDateString(),
            ],
            [
                'employer_id' => $employers[1]->id,
                'title' => 'Store Keeper',
                'main_category' => 'Operations',
                'sub_category' => 'Inventory',
                'location' => 'Galle',
                'type' => 'Full-time',
                'salary' => 'Rs. 70,000 - 95,000',
                'description' => 'Manage stock movements, warehouse records, and dispatch coordination for retail branches.',
                'requirements' => 'Inventory handling, MS Excel basics, honesty, punctuality, and physical stock counting.',
                'responsibilities' => 'Receive goods, update stock cards, prepare dispatch notes, and support audits.',
                'deadline' => now()->addWeeks(5)->toDateString(),
            ],
            [
                'employer_id' => $employers[3]->id,
                'title' => 'Graphic Designer',
                'main_category' => 'Creative',
                'sub_category' => 'Design',
                'location' => 'Colombo',
                'type' => 'Part-time',
                'salary' => 'Rs. 80,000 - 120,000',
                'description' => 'Design social creatives, banners, and campaign visuals for fast-moving brand accounts.',
                'requirements' => 'Photoshop, Illustrator or Figma, layout sense, Sinhala typography, and portfolio samples.',
                'responsibilities' => 'Create daily designs, resize campaign artwork, prepare brand assets, and meet deadlines.',
                'deadline' => now()->addWeeks(4)->toDateString(),
            ],
            [
                'employer_id' => $employers[2]->id,
                'title' => 'Customer Support Executive',
                'main_category' => 'Customer Service',
                'sub_category' => 'Call Center',
                'location' => 'Kandy',
                'type' => 'Full-time',
                'salary' => 'Rs. 80,000 - 115,000',
                'description' => 'Support banking customers through phone, email, and internal ticketing tools.',
                'requirements' => 'Clear Sinhala and English speaking, patience, basic computer literacy, and shift flexibility.',
                'responsibilities' => 'Resolve customer issues, document cases, and escalate urgent concerns to supervisors.',
                'deadline' => now()->addWeeks(3)->toDateString(),
            ],
            [
                'employer_id' => $employers[0]->id,
                'title' => 'Data Entry Operator',
                'main_category' => 'Administration',
                'sub_category' => 'Data Entry',
                'location' => 'Matara',
                'type' => 'Contract',
                'salary' => 'Rs. 65,000 - 85,000',
                'description' => 'Enter, clean, and verify product and customer data for business operations.',
                'requirements' => 'Fast typing, Excel basics, accuracy, and ability to follow documented processes.',
                'responsibilities' => 'Update records, check duplicates, prepare daily summaries, and flag data issues.',
                'deadline' => now()->addWeeks(2)->toDateString(),
            ],
            [
                'employer_id' => $employers[1]->id,
                'title' => 'HR Executive',
                'main_category' => 'Human Resources',
                'sub_category' => 'Recruitment',
                'location' => 'Colombo',
                'type' => 'Full-time',
                'salary' => 'Rs. 130,000 - 190,000',
                'description' => 'Coordinate recruitment, onboarding, attendance, and employee documentation.',
                'requirements' => 'HR diploma or degree, labour law basics, Excel, and confident communication.',
                'responsibilities' => 'Screen candidates, schedule interviews, maintain HR files, and support payroll inputs.',
                'deadline' => now()->addWeeks(6)->toDateString(),
            ],
            [
                'employer_id' => $employers[2]->id,
                'title' => 'Credit Analyst',
                'main_category' => 'Finance',
                'sub_category' => 'Credit',
                'location' => 'Colombo',
                'type' => 'Full-time',
                'salary' => 'Rs. 160,000 - 240,000',
                'description' => 'Assess lending applications and prepare credit recommendations for SME customers.',
                'requirements' => 'Finance degree, credit analysis knowledge, Excel, and report writing ability.',
                'responsibilities' => 'Review documents, analyse repayment capacity, prepare notes, and monitor risk flags.',
                'deadline' => now()->addWeeks(7)->toDateString(),
            ],
        ];

        Job::insert(array_map(fn (array $job) => array_merge($job, [
            'status' => 'active',
            'approved_at' => now(),
            'approved_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]), $jobs));
    }
}
