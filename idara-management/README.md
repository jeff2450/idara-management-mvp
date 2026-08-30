# Idara Management System — Awamu ya 1, 2, na 3

Hii ni codebase ya Laravel inayotekeleza **Awamu ya 1, 2, na 3 zote** kama
zilivyoainishwa kwenye `prd.md` §5 na `architecture.md` §2: Auth + roles 3, CRUD ya
idara, department scoping, SMS ya kulengwa, uzalishaji wa barua (PDF), kumbukumbu za
miamala, ratiba ya mwaka, shughuli, na ripoti za kiotomatiki.

## ⚠️ Muhimu kuhusu jinsi hii ilivyotengenezwa

Mazingira niliyotumia kuandika code hii **hayana ufikiaji wa packagist.org**, hivyo
sikuweza kuendesha `composer install` kuthibitisha kila kitu kinajengeka (build)
mwisho hadi mwisho dhidi ya framework halisi ya Laravel (au dompdf hasa - module za
Barua/Ripoti zinaitegemea sana). Nimeandika kila faili kwa mkono kufuatana na
makubaliano ya Laravel 11/12 (angalia `stacks.md`), na nimehakiki syntax ya kila
faili ya PHP kwa `php -l` (zote 47 zimepita bila hitilafu) — lakini bado unahitaji
kuendesha hatua za usanidi hapo chini kwenye mazingira yako mwenyewe (ambako una
ufikiaji wa Packagist) kabla ya kutumia kwa production, hasa `php artisan test` kwa
moduli za PDF ambazo sikuweza kuzijaribu bila dompdf halisi. Napendekeza umpe mtu
anayejua Laravel vizuri apitie code kabla ya kuipeleka kwenye server ya serikali.

## Usanidi (Setup)

```bash
composer install
cp .env.example .env
php artisan key:generate

# Hariri .env - weka DB_DATABASE/DB_USERNAME/DB_PASSWORD za MySQL yako
# (au badilisha DB_CONNECTION=sqlite kwa majaribio ya haraka - angalia .env.example)

php artisan migrate --seed
php artisan storage:link
php artisan serve

# SMS zinatumwa kupitia Laravel Queue (angalia architecture-essentials.md
# "Usiruhusu SMS itumwe bila kupitia job ya queue") - endesha worker kwenye
# terminal nyingine:
php artisan queue:work
```

Baada ya `--seed`, utapata:
- **Roles 3**: `admin`, `idara_leader`, `member`
- **Akaunti ya Admin**: `admin@idara.test` / `password` — **badilisha nenosiri hili
  mara moja** kabla ya kuweka mfumo live
- **Idara 5** kutoka SRS §2: Watoto, Wamama, Vijana, Kusifu na Kuabudu, Mashemasi
  (Idara ya Watoto imewekwa `is_sensitive = true` - angalia sehemu ya "Ulinzi wa
  Ziada" hapo chini)
- **Template moja ya mfano ya barua** ("Barua ya Utambulisho")

### SMS Gateway

Default ni `SMS_DRIVER=log` (haiiti mtandao - inaandika SMS kwenye
`storage/logs/laravel.log` tu). Ukiwa tayari na akaunti ya Beem Africa au NextSMS,
weka kwenye `.env`:

```
SMS_DRIVER=beem   # au: nextsms
BEEM_API_KEY=...
BEEM_SECRET_KEY=...
BEEM_SENDER_ID=INFO
```

**Kabla ya production**, thibitisha muundo halisi wa request/response wa Beem/NextSMS
API dhidi ya nyaraka zao za sasa - angalia maoni kwenye `app/Services/Sms/BeemSmsGateway.php`
na `NextSmsGateway.php`.

## Kuendesha vipimo (Tests)

```bash
php artisan test
```

- `tests/Feature/DepartmentScopingTest.php` — member/kiongozi mmoja hawezi kuona au
  kubadilisha data ya idara asiyomo (Awamu ya 1)
- `tests/Feature/ExtraProtectionsTest.php` — miamala ya fedha ni Kiongozi/Admin
  pekee (siyo mwanachama wa kawaida), na uanachama wa idara `is_sensitive` (Idara ya
  Watoto) ni Admin pekee kubadilisha
- `tests/Feature/SendDepartmentSmsTest.php` — job ya SMS inachuja recipients dhidi
  ya department_user hata kama ID za idara nyingine "zimeingizwa" kimakosa/kimakusudi

## Ramani ya Faili → Mahitaji (Traceability)

| Faili | Inatimiza |
|---|---|
| `app/Models/Scopes/DepartmentVisibilityScope.php` | prd.md §6.3 "Member anaona idara alizomo tu" |
| `app/Models/Scopes/DepartmentScope.php` + `Concerns/BelongsToDepartment.php` | architecture.md §3 - scoping ya SmsLog/Letter/DepartmentTransaction/AnnualSchedule/ActivityLog/Report |
| `app/Http/Middleware/EnsureDepartmentAccess.php` | architecture.md §2.1 |
| `app/Policies/DepartmentPolicy.php` | architecture.md §5 |
| `app/Policies/TransactionPolicy.php` | architecture.md §5 "ulinzi wa ziada...miamala ya fedha" - members wameondolewa kabisa |
| `departments.is_sensitive` + `DepartmentPolicy::manageMembers()` | architecture.md §5 "ulinzi wa ziada...Idara ya Watoto" |
| `app/Services/Sms/*` + `app/Jobs/SendDepartmentSms.php` | architecture.md §2.4 na §4.A |
| `app/Models/LetterTemplate.php`, `LetterController.php` | architecture.md §2.5 (mail-merge + dompdf) |
| `app/Http/Controllers/DepartmentTransactionController.php` | architecture.md §2.6 |
| `AnnualScheduleController.php`, `ActivityLogController.php` | architecture.md §2.7 |
| `app/Console/Commands/GenerateDepartmentReports.php` + `routes/console.php` | architecture.md §2.7/§6 "php artisan report:generate" + cron |

## Maswali Yaliyosalia Niliyoyafanyia Uamuzi

`prd.md` §10 na SRS §6 ziliacha maswali wazi. Ili niweze kujenga, nimechagua majibu
haya (rahisi kubadilisha baadaye - ni Policy/Form Request, siyo muundo mzima):

1. **Nani anaunda idara mpya?** → Admin pekee (`DepartmentPolicy::create`).
2. **Kiongozi anaweza kuteua kiongozi mwingine?** → Hapana — Admin pekee.
3. **Risiti za miamala zihitaji approval ya Admin?** → Hapana kwa sasa - Kiongozi
   anaingiza moja kwa moja (`TransactionPolicy::create`). Kuongeza hatua ya approval
   (mfano `status: pending|approved`) ni nyongeza ndogo kwenye
   `department_transactions` na `TransactionPolicy` ikibidi baadaye.
4. **Ulinzi wa ziada kwa Idara ya Watoto ni upi hasa?** → Nimechagua: (a) uanachama
   wa idara zilizowekwa `is_sensitive` ni Admin pekee kubadilisha (siyo kiongozi
   mwenyewe), na (b) miamala ya fedha (kwa idara ZOTE, siyo Watoto tu) ni
   Kiongozi/Admin pekee - angalia `TransactionPolicy`. Kama unataka masharti tofauti
   (mfano: Idara ya Watoto isionekane kabisa kwa non-leader hata kwenye orodha ya
   `/idara`), hii ni mabadiliko madogo kwenye `DepartmentVisibilityScope`.

## Kilichojengwa

### Awamu ya 1
- [x] Auth (login/logout, hakuna self-registration), Roles 3, CRUD ya Idara
- [x] Kuongeza mtumiaji (aliyepo au mpya) kwenye idara kama Kiongozi/Mwanachama
- [x] Global Scope + Policy zinazozuia data kuvuka idara
- [x] Dashibodi + vipimo vya department scoping

### Awamu ya 2
- [x] SMS ya kulengwa: `SmsGatewayInterface` (Beem/NextSMS/Log), job ya queue,
      `sms_logs` kwa audit trail
- [x] Barua: templates za mail-merge (Admin), uzalishaji wa PDF (dompdf), historia
      ya `letters` kwa idara
- [x] Miamala ya Idara: CRUD, **Kiongozi/Admin pekee** (ulinzi wa ziada wa fedha)

### Awamu ya 3
- [x] Ratiba ya Mwaka (`annual_schedules`) na Shughuli Zilizofanyika (`activity_logs`)
- [x] Ripoti za PDF zinazounganisha shughuli + miamala kwa kipindi husika, kwa
      mkono ("Zalisha Sasa") AU kiotomatiki kupitia `Schedule::command()` (cron)

## Ijayo (nje ya wigo kwa sasa - angalia prd.md §5.4)

Malipo ya mtandaoni (M-Pesa/gateway) na app ya simu (native) - kwa makusudi hazipo
kwa mujibu wa prd.md §5.4 "Nje ya Wigo".

