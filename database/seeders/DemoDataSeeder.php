<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ContactBook;
use App\Models\Contact;
use App\Models\UserAccess;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // 1. Create main Kontak Developers user
        $kontakUser = User::create([
            'name' => 'Kontak Developers',
            'email' => 'developers@kontak.app',
            'password' => Hash::make('password'),
            'phone_number' => '+1-555-0100',
            'address' => '123 Tech Street, Silicon Valley, CA 94000',
            'email_verified_at' => now(),
        ]);

        // Get their personal contact book (automatically created)
        $kontakContactBook = $kontakUser->allAccessibleContactBooksOrdered()->first();

        // 2. Create lots of contacts for Kontak Developers (2-6 per letter)
        $this->createContactsForUser($kontakContactBook);

        // 3. Create 5 admin users
        $adminUsers = [
            ['name' => 'Mark Flores', 'email' => 'mark.flores@kontak.app'],
            ['name' => 'Isaeus Guiang', 'email' => 'isaeus.guiang@kontak.app'],
            ['name' => 'Jedric Vicente', 'email' => 'jedric.vicente@kontak.app'],
            ['name' => 'Ceferino Arrey', 'email' => 'ceferino.arrey@kontak.app'],
            ['name' => 'Rainier Reyes', 'email' => 'rainier.reyes@kontak.app'],
        ];

        foreach ($adminUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Give admin access to Kontak Developers contact book
            UserAccess::create([
                'contact_book_id' => $kontakContactBook->id,
                'owner_id' => $kontakUser->id,
                'email' => $user->email,
                'role' => 'admin',
                'invited_at' => now(),
                'last_accessed_at' => now(),
            ]);
        }

        // 4. Create 3 audience users
        $audienceUsers = [
            ['name' => 'Marketing', 'email' => 'marketing@kontak.app'],
            ['name' => 'Sales', 'email' => 'sales@kontak.app'],
            ['name' => 'Customer Service', 'email' => 'support@kontak.app'],
        ];

        foreach ($audienceUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Give audience access to Kontak Developers contact book
            UserAccess::create([
                'contact_book_id' => $kontakContactBook->id,
                'owner_id' => $kontakUser->id,
                'email' => $user->email,
                'role' => 'audience',
                'invited_at' => now(),
                'last_accessed_at' => now(),
            ]);
        }

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('Main user: developers@kontak.app (password: password)');
        $this->command->info('Admin users: mark.flores@kontak.app, isaeus.guiang@kontak.app, etc. (password: password)');
        $this->command->info('Audience users: marketing@kontak.app, sales@kontak.app, support@kontak.app (password: password)');
    }

    private function createContactsForUser(ContactBook $contactBook): void
    {
        $contactsData = [
            // A (3 contacts)
            [
                'name' => 'Alexander Johnson',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0101'],
                    ['type' => 'work', 'number' => '+1-555-0102']
                ],
                'emails' => [
                    ['type' => 'personal', 'address' => 'alex.johnson@email.com'],
                    ['type' => 'work', 'address' => 'a.johnson@company.com']
                ],
                'websites' => [
                    ['type' => 'personal', 'url' => 'https://alexjohnson.dev'],
                    ['type' => 'portfolio', 'url' => 'https://github.com/alexjohnson']
                ],
                'address' => '456 Oak Avenue, Springfield, IL 62701',
                'notes' => 'Senior software engineer specializing in web development.'
            ],
            [
                'name' => 'Amanda Rodriguez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0103']],
                'emails' => [['type' => 'personal', 'address' => 'amanda.rodriguez@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://amandarodriguez.com']],
                'address' => '789 Pine Street, Denver, CO 80202',
                'notes' => 'Marketing specialist with 5 years of experience.'
            ],
            [
                'name' => 'Anthony Chen',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0104'],
                    ['type' => 'home', 'number' => '+1-555-0105']
                ],
                'emails' => [['type' => 'personal', 'address' => 'anthony.chen@email.com']],
                'websites' => null,
                'address' => '321 Maple Drive, Austin, TX 78701',
                'notes' => 'Data analyst and business intelligence expert.'
            ],

            // B (4 contacts)
            [
                'name' => 'Benjamin Taylor',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0106']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'ben.taylor@email.com'],
                    ['type' => 'work', 'address' => 'btaylor@techcorp.com']
                ],
                'websites' => [['type' => 'portfolio', 'url' => 'https://bentaylor.design']],
                'address' => '654 Cedar Lane, Portland, OR 97201',
                'notes' => 'UI/UX designer with expertise in mobile applications.'
            ],
            [
                'name' => 'Brianna Wilson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0107']],
                'emails' => [['type' => 'work', 'address' => 'brianna.wilson@consulting.com']],
                'websites' => [
                    ['type' => 'work', 'url' => 'https://briwilson.consulting'],
                    ['type' => 'social', 'url' => 'https://linkedin.com/in/briannawilson']
                ],
                'address' => '987 Birch Road, Seattle, WA 98101',
                'notes' => 'Management consultant specializing in digital transformation.'
            ],
            [
                'name' => 'Bruce Anderson',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0108'],
                    ['type' => 'work', 'number' => '+1-555-0109']
                ],
                'emails' => [['type' => 'personal', 'address' => 'bruce.anderson@email.com']],
                'websites' => null,
                'address' => '147 Elm Street, Boston, MA 02101',
                'notes' => 'Project manager with PMP certification.'
            ],
            [
                'name' => 'Bella Garcia',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0110']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'bella.garcia@email.com'],
                    ['type' => 'work', 'address' => 'bgarcia@startup.io']
                ],
                'websites' => [['type' => 'portfolio', 'url' => 'https://bellagarcia.art']],
                'address' => '258 Willow Court, Miami, FL 33101',
                'notes' => 'Creative director and graphic designer.'
            ],

            // C (5 contacts)
            [
                'name' => 'Christopher Lee',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0111']],
                'emails' => [['type' => 'personal', 'address' => 'chris.lee@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://chrislee.dev']],
                'address' => '369 Spruce Avenue, Phoenix, AZ 85001',
                'notes' => 'Full-stack developer and tech lead.'
            ],
            [
                'name' => 'Catherine Brown',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0112'],
                    ['type' => 'work', 'number' => '+1-555-0113']
                ],
                'emails' => [['type' => 'work', 'address' => 'catherine.brown@legal.com']],
                'websites' => null,
                'address' => '741 Poplar Street, Chicago, IL 60601',
                'notes' => 'Corporate lawyer specializing in technology law.'
            ],
            [
                'name' => 'Carlos Martinez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0114']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'carlos.martinez@email.com'],
                    ['type' => 'work', 'address' => 'cmartinez@agency.com']
                ],
                'websites' => [
                    ['type' => 'portfolio', 'url' => 'https://carlosmartinez.photo'],
                    ['type' => 'social', 'url' => 'https://instagram.com/carlosmphoto']
                ],
                'address' => '852 Hickory Drive, Los Angeles, CA 90210',
                'notes' => 'Professional photographer and visual artist.'
            ],
            [
                'name' => 'Claire Thompson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0115']],
                'emails' => [['type' => 'work', 'address' => 'claire.thompson@finance.com']],
                'websites' => [['type' => 'work', 'url' => 'https://clairethompson.finance']],
                'address' => '963 Chestnut Lane, New York, NY 10001',
                'notes' => 'Financial advisor and investment consultant.'
            ],
            [
                'name' => 'Cameron Davis',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0116'],
                    ['type' => 'home', 'number' => '+1-555-0117']
                ],
                'emails' => [['type' => 'personal', 'address' => 'cameron.davis@email.com']],
                'websites' => null,
                'address' => '159 Sycamore Road, Nashville, TN 37201',
                'notes' => 'Music producer and sound engineer.'
            ],

            // D (3 contacts)
            [
                'name' => 'Daniel Miller',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0118']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'daniel.miller@email.com'],
                    ['type' => 'work', 'address' => 'dmiller@research.org']
                ],
                'websites' => [['type' => 'work', 'url' => 'https://danielmiller.research']],
                'address' => '753 Magnolia Street, Atlanta, GA 30301',
                'notes' => 'Research scientist in artificial intelligence.'
            ],
            [
                'name' => 'Diana White',
                'phones' => [['type' => 'work', 'number' => '+1-555-0119']],
                'emails' => [['type' => 'work', 'address' => 'diana.white@hospital.org']],
                'websites' => null,
                'address' => '486 Dogwood Circle, Dallas, TX 75201',
                'notes' => 'Pediatric nurse with 10 years of experience.'
            ],
            [
                'name' => 'David Kim',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0120'],
                    ['type' => 'work', 'number' => '+1-555-0121']
                ],
                'emails' => [['type' => 'personal', 'address' => 'david.kim@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://davidkim.arch']],
                'address' => '297 Redwood Avenue, San Francisco, CA 94102',
                'notes' => 'Architect specializing in sustainable design.'
            ],

            // E (2 contacts)
            [
                'name' => 'Emily Johnson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0122']],
                'emails' => [['type' => 'personal', 'address' => 'emily.johnson@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://emilyjohnson.writer']],
                'address' => '681 Cedar Ridge, Minneapolis, MN 55401',
                'notes' => 'Technical writer and content strategist.'
            ],
            [
                'name' => 'Ethan Rodriguez',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0123'],
                    ['type' => 'work', 'number' => '+1-555-0124']
                ],
                'emails' => [
                    ['type' => 'personal', 'address' => 'ethan.rodriguez@email.com'],
                    ['type' => 'work', 'address' => 'erodriguez@startup.com']
                ],
                'websites' => [['type' => 'work', 'url' => 'https://ethanrodriguez.coach']],
                'address' => '504 Pine Valley Drive, Salt Lake City, UT 84101',
                'notes' => 'Business coach and entrepreneur.'
            ],

            // F (4 contacts)
            [
                'name' => 'Fiona Chen',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0125']],
                'emails' => [['type' => 'work', 'address' => 'fiona.chen@design.studio']],
                'websites' => [
                    ['type' => 'portfolio', 'url' => 'https://fionachen.design'],
                    ['type' => 'social', 'url' => 'https://behance.net/fionachen']
                ],
                'address' => '816 Maple Grove, Portland, OR 97202',
                'notes' => 'Interior designer with focus on modern spaces.'
            ],
            [
                'name' => 'Frank Wilson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0126']],
                'emails' => [['type' => 'work', 'address' => 'frank.wilson@construction.com']],
                'websites' => null,
                'address' => '429 Oak Hill Road, Cleveland, OH 44101',
                'notes' => 'Construction project manager and contractor.'
            ],
            [
                'name' => 'Felicity Taylor',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0127'],
                    ['type' => 'home', 'number' => '+1-555-0128']
                ],
                'emails' => [['type' => 'personal', 'address' => 'felicity.taylor@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://felicitytaylor.edu']],
                'address' => '372 University Boulevard, Ann Arbor, MI 48101',
                'notes' => 'University professor in computer science.'
            ],
            [
                'name' => 'Felix Garcia',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0129']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'felix.garcia@email.com'],
                    ['type' => 'work', 'address' => 'fgarcia@agency.net']
                ],
                'websites' => [['type' => 'portfolio', 'url' => 'https://felixgarcia.digital']],
                'address' => '695 Sunset Drive, Las Vegas, NV 89101',
                'notes' => 'Digital marketing specialist and SEO expert.'
            ],

            // Continue with more letters... (adding a few more key ones)

            // G (3 contacts)
            [
                'name' => 'Grace Anderson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0130']],
                'emails' => [['type' => 'work', 'address' => 'grace.anderson@nonprofit.org']],
                'websites' => [['type' => 'work', 'url' => 'https://graceanderson.nonprofit']],
                'address' => '108 Community Street, Sacramento, CA 95814',
                'notes' => 'Nonprofit organization director and activist.'
            ],
            [
                'name' => 'Gabriel Martinez',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0131'],
                    ['type' => 'work', 'number' => '+1-555-0132']
                ],
                'emails' => [['type' => 'personal', 'address' => 'gabriel.martinez@email.com']],
                'websites' => null,
                'address' => '567 Highland Avenue, Pittsburgh, PA 15201',
                'notes' => 'Mechanical engineer specializing in robotics.'
            ],
            [
                'name' => 'Gina Thompson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0133']],
                'emails' => [['type' => 'work', 'address' => 'gina.thompson@media.com']],
                'websites' => [
                    ['type' => 'work', 'url' => 'https://ginathompson.media'],
                    ['type' => 'social', 'url' => 'https://twitter.com/ginathompson']
                ],
                'address' => '890 Media Plaza, Los Angeles, CA 90028',
                'notes' => 'Television producer and media executive.'
            ],

            // H (2 contacts)
            [
                'name' => 'Henry Davis',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0134']],
                'emails' => [['type' => 'personal', 'address' => 'henry.davis@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://henrydavis.chef']],
                'address' => '234 Culinary Street, New Orleans, LA 70112',
                'notes' => 'Executive chef and restaurant owner.'
            ],
            [
                'name' => 'Hannah Miller',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0135'],
                    ['type' => 'work', 'number' => '+1-555-0136']
                ],
                'emails' => [['type' => 'work', 'address' => 'hannah.miller@vet.clinic']],
                'websites' => null,
                'address' => '789 Animal Care Drive, Indianapolis, IN 46201',
                'notes' => 'Veterinarian specializing in small animals.'
            ],

            // I (3 contacts)
            [
                'name' => 'Isaac White',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0137']],
                'emails' => [['type' => 'personal', 'address' => 'isaac.white@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://isaacwhite.music']],
                'address' => '456 Harmony Lane, Nashville, TN 37203',
                'notes' => 'Professional musician and composer.'
            ],
            [
                'name' => 'Isabella Kim',
                'phones' => [['type' => 'work', 'number' => '+1-555-0138']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'isabella.kim@email.com'],
                    ['type' => 'work', 'address' => 'ikim@fashion.house']
                ],
                'websites' => [['type' => 'work', 'url' => 'https://isabellakimfashion.com']],
                'address' => '123 Fashion Avenue, New York, NY 10018',
                'notes' => 'Fashion designer and stylist.'
            ],
            [
                'name' => 'Ivan Rodriguez',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0139'],
                    ['type' => 'home', 'number' => '+1-555-0140']
                ],
                'emails' => [['type' => 'personal', 'address' => 'ivan.rodriguez@email.com']],
                'websites' => null,
                'address' => '678 Sports Complex, Phoenix, AZ 85003',
                'notes' => 'Professional athlete and sports trainer.'
            ],

            // J (4 contacts)
            [
                'name' => 'Jessica Johnson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0141']],
                'emails' => [['type' => 'work', 'address' => 'jessica.johnson@law.firm']],
                'websites' => [['type' => 'work', 'url' => 'https://jessicajohnson.law']],
                'address' => '901 Legal Plaza, Washington, DC 20001',
                'notes' => 'Immigration lawyer and legal advocate.'
            ],
            [
                'name' => 'James Chen',
                'phones' => [['type' => 'work', 'number' => '+1-555-0142']],
                'emails' => [['type' => 'work', 'address' => 'james.chen@hospital.med']],
                'websites' => null,
                'address' => '345 Medical Center Drive, Houston, TX 77030',
                'notes' => 'Cardiac surgeon with 15 years of experience.'
            ],
            [
                'name' => 'Julia Taylor',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0143'],
                    ['type' => 'work', 'number' => '+1-555-0144']
                ],
                'emails' => [['type' => 'personal', 'address' => 'julia.taylor@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://juliataylor.art']],
                'address' => '567 Gallery District, Santa Fe, NM 87501',
                'notes' => 'Fine artist and gallery curator.'
            ],
            [
                'name' => 'Jordan Anderson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0145']],
                'emails' => [
                    ['type' => 'personal', 'address' => 'jordan.anderson@email.com'],
                    ['type' => 'work', 'address' => 'janderson@real.estate']
                ],
                'websites' => [['type' => 'work', 'url' => 'https://jordananderson.realty']],
                'address' => '890 Property Lane, Orlando, FL 32801',
                'notes' => 'Real estate agent specializing in commercial properties.'
            ],
        ];

        // Add contacts for letters K through Z with 2-3 contacts each
        $moreContacts = [
            // K (2 contacts)
            [
                'name' => 'Kevin Brown',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0146']],
                'emails' => [['type' => 'personal', 'address' => 'kevin.brown@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://kevinbrown.consulting']],
                'address' => '123 Business Park, Richmond, VA 23230',
                'notes' => 'Business consultant and strategy advisor.'
            ],
            [
                'name' => 'Katherine Wilson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0147']],
                'emails' => [['type' => 'work', 'address' => 'katherine.wilson@school.edu']],
                'websites' => null,
                'address' => '456 Education Boulevard, Columbus, OH 43215',
                'notes' => 'Elementary school principal and educator.'
            ],

            // L (3 contacts)
            [
                'name' => 'Lucas Martinez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0148']],
                'emails' => [['type' => 'personal', 'address' => 'lucas.martinez@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://lucasmartinez.film']],
                'address' => '789 Cinema Drive, Los Angeles, CA 90038',
                'notes' => 'Film director and cinematographer.'
            ],
            [
                'name' => 'Lily Garcia',
                'phones' => [['type' => 'work', 'number' => '+1-555-0149']],
                'emails' => [['type' => 'work', 'address' => 'lily.garcia@pharmacy.com']],
                'websites' => null,
                'address' => '321 Health Street, Phoenix, AZ 85004',
                'notes' => 'Licensed pharmacist and healthcare professional.'
            ],
            [
                'name' => 'Logan Davis',
                'phones' => [
                    ['type' => 'mobile', 'number' => '+1-555-0150'],
                    ['type' => 'work', 'number' => '+1-555-0151']
                ],
                'emails' => [['type' => 'personal', 'address' => 'logan.davis@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://logandavis.security']],
                'address' => '654 Cyber Lane, Austin, TX 78702',
                'notes' => 'Cybersecurity specialist and ethical hacker.'
            ],

            // M (2 contacts)
            [
                'name' => 'Maya Thompson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0152']],
                'emails' => [['type' => 'personal', 'address' => 'maya.thompson@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://mayathompson.yoga']],
                'address' => '987 Wellness Center, Portland, OR 97205',
                'notes' => 'Certified yoga instructor and wellness coach.'
            ],
            [
                'name' => 'Michael Lee',
                'phones' => [['type' => 'work', 'number' => '+1-555-0153']],
                'emails' => [['type' => 'work', 'address' => 'michael.lee@finance.bank']],
                'websites' => null,
                'address' => '147 Financial District, New York, NY 10005',
                'notes' => 'Investment banker and financial analyst.'
            ],

            // N (2 contacts)
            [
                'name' => 'Natalie White',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0154']],
                'emails' => [['type' => 'personal', 'address' => 'natalie.white@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://nataliewhite.therapy']],
                'address' => '258 Healing Way, San Diego, CA 92101',
                'notes' => 'Licensed therapist specializing in family counseling.'
            ],
            [
                'name' => 'Nathan Kim',
                'phones' => [['type' => 'work', 'number' => '+1-555-0155']],
                'emails' => [['type' => 'work', 'address' => 'nathan.kim@aerospace.com']],
                'websites' => null,
                'address' => '369 Aerospace Parkway, Seattle, WA 98109',
                'notes' => 'Aerospace engineer working on satellite technology.'
            ],

            // O (2 contacts)
            [
                'name' => 'Olivia Rodriguez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0156']],
                'emails' => [['type' => 'personal', 'address' => 'olivia.rodriguez@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://oliviarodriguez.photo']],
                'address' => '741 Studio Row, Nashville, TN 37204',
                'notes' => 'Wedding photographer and event specialist.'
            ],
            [
                'name' => 'Oscar Chen',
                'phones' => [['type' => 'work', 'number' => '+1-555-0157']],
                'emails' => [['type' => 'work', 'address' => 'oscar.chen@restaurant.com']],
                'websites' => [['type' => 'work', 'url' => 'https://oscarchen.restaurant']],
                'address' => '852 Culinary Square, San Francisco, CA 94103',
                'notes' => 'Restaurant owner and culinary entrepreneur.'
            ],

            // P-Z (2 contacts each for remaining letters)
            [
                'name' => 'Patricia Taylor',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0158']],
                'emails' => [['type' => 'personal', 'address' => 'patricia.taylor@email.com']],
                'websites' => null,
                'address' => '963 Garden Lane, Charleston, SC 29401',
                'notes' => 'Landscape architect and urban planner.'
            ],
            [
                'name' => 'Peter Anderson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0159']],
                'emails' => [['type' => 'work', 'address' => 'peter.anderson@insurance.com']],
                'websites' => [['type' => 'work', 'url' => 'https://peteranderson.insurance']],
                'address' => '159 Insurance Plaza, Hartford, CT 06103',
                'notes' => 'Insurance broker specializing in commercial policies.'
            ],

            // Q (2 contacts)
            [
                'name' => 'Quinn Miller',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0160']],
                'emails' => [['type' => 'personal', 'address' => 'quinn.miller@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://quinnmiller.design']],
                'address' => '753 Design District, Portland, OR 97209',
                'notes' => 'Graphic designer and brand strategist.'
            ],
            [
                'name' => 'Quincy Davis',
                'phones' => [['type' => 'work', 'number' => '+1-555-0161']],
                'emails' => [['type' => 'work', 'address' => 'quincy.davis@library.org']],
                'websites' => null,
                'address' => '486 Library Square, Boston, MA 02108',
                'notes' => 'Head librarian and information specialist.'
            ],

            // R (2 contacts)
            [
                'name' => 'Rachel White',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0162']],
                'emails' => [['type' => 'personal', 'address' => 'rachel.white@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://rachelwhite.travel']],
                'address' => '297 Adventure Avenue, Denver, CO 80205',
                'notes' => 'Travel agent and adventure tour guide.'
            ],
            [
                'name' => 'Robert Kim',
                'phones' => [['type' => 'work', 'number' => '+1-555-0163']],
                'emails' => [['type' => 'work', 'address' => 'robert.kim@accounting.firm']],
                'websites' => null,
                'address' => '681 Financial Street, Chicago, IL 60604',
                'notes' => 'Certified public accountant and tax advisor.'
            ],

            // S (2 contacts)
            [
                'name' => 'Sophia Garcia',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0164']],
                'emails' => [['type' => 'personal', 'address' => 'sophia.garcia@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://sophiagarcia.dance']],
                'address' => '504 Performing Arts Center, Miami, FL 33132',
                'notes' => 'Professional dancer and choreographer.'
            ],
            [
                'name' => 'Samuel Rodriguez',
                'phones' => [['type' => 'work', 'number' => '+1-555-0165']],
                'emails' => [['type' => 'work', 'address' => 'samuel.rodriguez@news.tv']],
                'websites' => [['type' => 'work', 'url' => 'https://samuelrodriguez.news']],
                'address' => '816 Media Center, Atlanta, GA 30309',
                'notes' => 'Television news anchor and journalist.'
            ],

            // T (2 contacts)
            [
                'name' => 'Tiffany Martinez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0166']],
                'emails' => [['type' => 'personal', 'address' => 'tiffany.martinez@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://tiffanymartinez.wellness']],
                'address' => '429 Health Plaza, Phoenix, AZ 85006',
                'notes' => 'Nutritionist and wellness consultant.'
            ],
            [
                'name' => 'Timothy Johnson',
                'phones' => [['type' => 'work', 'number' => '+1-555-0167']],
                'emails' => [['type' => 'work', 'address' => 'timothy.johnson@university.edu']],
                'websites' => null,
                'address' => '372 Academic Way, Ann Arbor, MI 48109',
                'notes' => 'University professor of environmental science.'
            ],

            // U (2 contacts)
            [
                'name' => 'Ursula Chen',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0168']],
                'emails' => [['type' => 'personal', 'address' => 'ursula.chen@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://ursula-chen.art']],
                'address' => '695 Artist Quarter, Santa Fe, NM 87505',
                'notes' => 'Sculptor and mixed media artist.'
            ],
            [
                'name' => 'Ulysses Brown',
                'phones' => [['type' => 'work', 'number' => '+1-555-0169']],
                'emails' => [['type' => 'work', 'address' => 'ulysses.brown@military.gov']],
                'websites' => null,
                'address' => '108 Defense Boulevard, Norfolk, VA 23511',
                'notes' => 'Military officer and defense contractor.'
            ],

            // V (2 contacts)
            [
                'name' => 'Victoria Wilson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0170']],
                'emails' => [['type' => 'personal', 'address' => 'victoria.wilson@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://victoriawilson.events']],
                'address' => '567 Event Center, Las Vegas, NV 89102',
                'notes' => 'Event planner specializing in corporate events.'
            ],
            [
                'name' => 'Vincent Taylor',
                'phones' => [['type' => 'work', 'number' => '+1-555-0171']],
                'emails' => [['type' => 'work', 'address' => 'vincent.taylor@automotive.com']],
                'websites' => null,
                'address' => '890 Auto Row, Detroit, MI 48201',
                'notes' => 'Automotive engineer and product designer.'
            ],

            // W (2 contacts)
            [
                'name' => 'Wendy Anderson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0172']],
                'emails' => [['type' => 'personal', 'address' => 'wendy.anderson@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://wendyanderson.social']],
                'address' => '234 Social Media Drive, San Francisco, CA 94107',
                'notes' => 'Social media manager and digital marketer.'
            ],
            [
                'name' => 'William Davis',
                'phones' => [['type' => 'work', 'number' => '+1-555-0173']],
                'emails' => [['type' => 'work', 'address' => 'william.davis@construction.co']],
                'websites' => [['type' => 'work', 'url' => 'https://williamdavis.construction']],
                'address' => '789 Building Lane, Houston, TX 77002',
                'notes' => 'General contractor and construction manager.'
            ],

            // X (2 contacts)
            [
                'name' => 'Ximena Rodriguez',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0174']],
                'emails' => [['type' => 'personal', 'address' => 'ximena.rodriguez@email.com']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://ximenarodriguez.fashion']],
                'address' => '456 Fashion District, Los Angeles, CA 90014',
                'notes' => 'Fashion designer and textile artist.'
            ],
            [
                'name' => 'Xavier Miller',
                'phones' => [['type' => 'work', 'number' => '+1-555-0175']],
                'emails' => [['type' => 'work', 'address' => 'xavier.miller@biotech.com']],
                'websites' => null,
                'address' => '321 Research Park, Cambridge, MA 02139',
                'notes' => 'Biotechnology researcher and lab director.'
            ],

            // Y (2 contacts)
            [
                'name' => 'Yolanda Garcia',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0176']],
                'emails' => [['type' => 'personal', 'address' => 'yolanda.garcia@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://yolandagarcia.translate']],
                'address' => '654 Language Center, Miami, FL 33130',
                'notes' => 'Professional translator and interpreter.'
            ],
            [
                'name' => 'Yuki Tanaka',
                'phones' => [['type' => 'work', 'number' => '+1-555-0177']],
                'emails' => [['type' => 'work', 'address' => 'yuki.tanaka@tech.startup']],
                'websites' => [['type' => 'portfolio', 'url' => 'https://yukitanaka.ai']],
                'address' => '987 Innovation Hub, San Jose, CA 95110',
                'notes' => 'AI researcher and machine learning engineer.'
            ],

            // Z (2 contacts)
            [
                'name' => 'Zachary Thompson',
                'phones' => [['type' => 'mobile', 'number' => '+1-555-0178']],
                'emails' => [['type' => 'personal', 'address' => 'zachary.thompson@email.com']],
                'websites' => [['type' => 'work', 'url' => 'https://zacharythompson.sports']],
                'address' => '147 Athletic Complex, Orlando, FL 32819',
                'notes' => 'Professional sports coach and athletic trainer.'
            ],
            [
                'name' => 'Zoe Martinez',
                'phones' => [['type' => 'work', 'number' => '+1-555-0179']],
                'emails' => [['type' => 'work', 'address' => 'zoe.martinez@environmental.org']],
                'websites' => null,
                'address' => '258 Green Way, Seattle, WA 98101',
                'notes' => 'Environmental scientist and conservation advocate.'
            ],
        ];

        // Combine all contacts
        $allContacts = array_merge($contactsData, $moreContacts);

        // Create contacts in database
        foreach ($allContacts as $contactData) {
            Contact::create([
                'contact_book_id' => $contactBook->id,
                'name' => $contactData['name'],
                'phones' => $contactData['phones'],
                'emails' => $contactData['emails'],
                'websites' => $contactData['websites'],
                'address' => $contactData['address'],
                'notes' => $contactData['notes'],
            ]);
        }
    }
}
