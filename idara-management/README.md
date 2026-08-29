# Idara Management System — Awamu ya 1 (MVP)

Hii ni codebase ya Laravel inayotekeleza **Awamu ya 1 (MVP)** kama ilivyoainishwa
kwenye `architecture-essentials.md` na `prd.md` §5.1: Auth + roles 3, CRUD ya idara,
kuunganisha kiongozi/wanachama kwenye idara, department scoping (Global Scope +
Policy), na dashibodi rahisi.

**SMS, barua, kumbukumbu za miamala, na ripoti (Awamu ya 2 na 3) hazijajengwa bado
kwa makusudi** — angalia `architecture-essentials.md`: *"Usianze na SMS/barua/ripoti
kabla ya (1)–(5) kufanya kazi vizuri na kupimwa."* Muundo wa msingi (trait
`BelongsToDepartment` + `DepartmentScope`) tayari upo tayari kwa awamu hizo.

## ⚠️ Muhimu kuhusu jinsi hii ilivyotengenezwa

Mazingira niliyotumia kuandika code hii **hayana ufikiaji wa packagist.org**, hivyo
sikuweza kuendesha `composer install` kuthibitisha kila kitu kinajengeka (build)
mwisho hadi mwisho dhidi ya framework halisi ya Laravel. Nimeandika kila faili kwa
mkono kufuatana na makubaliano ya Laravel 11/12 (angalia `stacks.md`), na
nimehakiki syntax ya kila faili ya PHP kwa `php -l` (zote zimepita bila hitilafu) —
lakini bado unahitaji kuendesha hatua za usanidi hapo chini kwenye mazingira yako
mwenyewe (ambako una ufikiaji wa Packagist) kabla ya kutumia kwa production, na
napendekeza umpe mtu anayejua Laravel vizuri apitie code kabla ya kuipeleka kwenye
server ya serikali.

## Usanidi (Setup)

```bash
composer install
cp .env.example .env
php artisan key:generate

# Hariri .env - weka DB_DATABASE/DB_USERNAME/DB_PASSWORD za MySQL yako
# (au badilisha DB_CONNECTION=sqlite kwa majaribio ya haraka - angalia .env.example)

php artisan migrate --seed
php artisan serve
```

Baada ya `--seed`, utapata:
- **Roles 3**: `admin`, `idara_leader`, `member`
- **Akaunti ya Admin**: `admin@idara.test` / `password` — **badilisha nenosiri hili
  mara moja** kabla ya kuweka mfumo live
- **Idara 5** kutoka SRS §2: Watoto, Wamama, Vijana, Kusifu na Kuabudu, Mashemasi
  (bila viongozi bado — ingia kama Admin uende `/idara` kuwateua)

## Kuendesha vipimo (Tests)

```bash
php artisan test
```

`tests/Feature/DepartmentScopingTest.php` inathibitisha sheria muhimu zaidi ya
mfumo mzima: kwamba member/kiongozi mmoja hawezi kuona au kubadilisha data ya idara
asiyomo (prd.md §6.2–6.3, architecture.md §5).

## Ramani ya Faili → Mahitaji (Traceability)

| Faili | Inatimiza |
|---|---|
| `app/Models/Scopes/DepartmentVisibilityScope.php` | prd.md §6.3 "Member anaona idara alizomo tu" |
| `app/Models/Scopes/DepartmentScope.php` + `Concerns/BelongsToDepartment.php` | Muundo wa scoping kwa Awamu ya 2/3 (SmsLog, Letter, n.k.) - architecture.md §3 |
| `app/Http/Middleware/EnsureDepartmentAccess.php` | architecture.md §2.1 "Middleware ya EnsureDepartmentAccess" |
| `app/Policies/DepartmentPolicy.php` | architecture.md §5 "Policy classes kwa kila model muhimu" |
| `database/migrations/*_create_department_user_table.php` | architecture.md §2.1/§3 "department_user" |
| `database/seeders/DepartmentSeeder.php` | SRS §2 "Idara za awali zilizotajwa" |
| `app/Http/Controllers/DepartmentMemberController.php` | prd.md §6.2 "Kiongozi anaweza kuhusishwa na idara zaidi ya moja" + SRS §4.3 |

## Maswali Yaliyosalia Niliyoyafanyia Uamuzi kwa MVP

`prd.md` §10 iliacha maswali wazi. Ili niweze kujenga, nimechagua majibu haya
(rahisi kubadilisha baadaye):

1. **Nani anaunda idara mpya?** → Admin pekee (`DepartmentPolicy::create`).
2. **Kiongozi anaweza kuteua kiongozi mwingine?** → Hapana — kuteua "leader" ni
   Admin pekee (`StoreDepartmentMemberRequest::withValidator`); kiongozi anaweza
   kuongeza "member" kwenye idara yake pekee.
3. **Risiti za miamala zihitaji approval?** → Halijashughulikiwa bado (ni Awamu
   ya 2 — department_transactions).

Kama majibu haya si sahihi kwa mradi wako, ni mabadiliko madogo ya Policy/Form
Request, siyo mabadiliko ya muundo mzima.

## Kilichojengwa (Awamu ya 1)

- [x] Auth (login/logout ya kujitegemea, hakuna self-registration — Admin
      anaunda akaunti)
- [x] Roles 3 kupitia `spatie/laravel-permission`
- [x] CRUD kamili ya Idara (Admin pekee kwa unda/hariri/futa)
- [x] Kuongeza mtumiaji aliyepo (kwa email) au mtumiaji mpya kwenye idara, kama
      Kiongozi au Mwanachama
- [x] Global Scope + Policy zinazozuia data kuvuka idara
- [x] Dashibodi inayoonyesha idara za mtumiaji aliyeingia pekee
- [x] Vipimo (tests) vinavyothibitisha department scoping

## Ijayo (Awamu ya 2/3 - bado hazijaanza)

SMS ya kulengwa, uzalishaji wa barua (DomPDF), kumbukumbu za miamala, ratiba ya
mwaka, na ripoti za kiotomatiki — angalia `architecture.md` §2.4–2.7 kwa muundo
uliopangwa tayari.
