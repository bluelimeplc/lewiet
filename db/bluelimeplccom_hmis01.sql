-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 05, 2026 at 03:34 PM
-- Server version: 10.11.19-MariaDB-cll-lve
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bluelimeplccom_hmis01`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `avg_patient_tat_per_minuite`
-- (See below for the actual view)
--
CREATE TABLE `avg_patient_tat_per_minuite` (
`WithOutAppointment` varchar(18)
,`created_at` datetime /* mariadb-5.3 */
,`doctor_name` varchar(50)
,`UpToDoctor` decimal(24,4)
,`TotalStayedTime` decimal(24,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_allergies`
--

CREATE TABLE `hospital_allergies` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_appointments`
--

CREATE TABLE `hospital_appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_reason` varchar(50) NOT NULL,
  `appointment_status` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `paid_appointment` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `reminder_sent` int(11) NOT NULL,
  `mobile` varchar(50) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `time` varchar(50) NOT NULL,
  `cancelled_by` varchar(50) DEFAULT NULL,
  `cancelation_reason` varchar(200) DEFAULT NULL,
  `custom_note` varchar(500) NOT NULL,
  `event_id` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_approved_doctors_report`
--

CREATE TABLE `hospital_approved_doctors_report` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `report_date` date NOT NULL,
  `report_data` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_auto_complete`
--

CREATE TABLE `hospital_auto_complete` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `type` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_auto_complete`
--

INSERT INTO `hospital_auto_complete` (`id`, `name`, `data`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'diagnosis', '[\"Acanthosis Nigricans\", \"acne\", \"Acne conglobata\", \"Acne fulminans\", \"Acne keloidalis nuchae\", \"Acne like Irruption \", \"Acne neonatorum\", \"Acne Rosacea\", \"Acne Rosacea with mlid Rynophyma\", \"Acne scars\", \"Acne vulgaris – mild\", \"Acne vulgaris – moderate\", \"Acne vulgaris – severe\", \"Acne Vulgaris & Post inflammatory Hyperpigmentation\", \"Acquired elastosis perforans serpiginosa (AEPS)\", \"Acral vitiligo\", \"Acrocyanosis\", \"Acrodermatitis continua\", \"Acrokeratosis verruciformis of Hopf (AKV)\", \"Actinic cheilitis\", \"Actinic chelitis\", \"Actinic keratosis\", \"Actinic Lichen planus\", \"Actinic prurigo\", \"Acute cutaneous lupus\", \"Acute febrile neutrophilic dermatosis (Sweet syndrome)\", \"Acute generalised exanthematous pustulosis (AGEP)\", \"Acute intermittent porphyria\", \"Acute Urticaria\", \"AIDS related exanthem\", \"AIDS related xerosis\", \"Ainhum\", \"Albinism\", \"Allergi Rhenitis\", \"Allergic contact dermatitis\", \"Allergic Rehenitis\", \"Alopecia areata﻿\", \"Amyloidosis\", \"Anagen Effluvium\", \"androgen\", \"Androgenetic Alopecia\", \"Anetoderma\", \"Angioedema\", \"ANGIOEDEMA\", \"Angiofibroma\", \"Angiokeratoma\", \"Angiolymphoid hyperplasia with eosinophilia\", \"Angioma serpiginosum\", \"Angiosarcoma\", \"Angora hair nevus\", \"Angular cheilitis\", \"Anogenital wart\", \"Antiphospholipid syndrome\", \"Aphtous ulcer\", \"Aplasia Cutis Congenita\", \"Appendageal Tumor\", \"Aquagenic urticaria\", \"Aspergillosis\", \"Asteatotic eczema\", \"ataxia telangiectasia syndrome\", \"Ataxia-telangiectasia\", \"Athlete\'s foot﻿\", \"atopic\", \"Atopic dermatitis (Eczema)\", \"Atopic Diathesis\", \"Atopic Diathesis with severe Xerotic Skin\", \"Atopic Eczema & common Warts\", \"Atopic eczema & Tinea pedis\", \"Atopic Eczema with 2ndory Bacterial Infection\", \"Atopic eruption of pregnancy\", \"Atopic hand dermatitis\", \"Atrophic glossitis\", \"Atrophic scars\", \"Atrophoderma\", \"Autosensitization Dermatitis\", \"Bacterial Vaginosis (BV)\", \"Balanoposthitis\", \"Basal cell carcinoma\", \"basal cell nevus syndrome\", \"Beau\'s lines\", \"Becker naevus\", \"Bed sore\", \"Behcet disease\", \"Benign Cephalic Pustulosis\", \"Benign neonatal cephalic pustulosis\", \"Birthmark\", \"Blastomycosis\", \"Blepharitis\", \"Blister beetle dermatitis\", \"Bloch-Sulzberger syndrome\", \"Bloom syndrome\", \"Blue nevus\", \"Body Dysmorphic Disorder\", \"Boils (furuncles)\", \"brittle bone disease\", \"Bromhidrosis\", \"Bullous drug eruptions\", \"Bullous pemphigoid\", \"café au lait spots\", \"Calcinosis cutis\", \"Candidal balanitis\", \"Carbuncles\", \"Cellulitis\", \"Cellulitis\", \"Central centrifugal cicatricial alopecia\", \"Chelitis\", \"Chemical peels\", \"Chemotherapy Induced Alopecia\", \"Cherry angioma\", \"Chickenpox\", \"Chickenpox (varicella)\", \"Chilblain cutaneous lupus\", \"Chlamydia\", \"Cholinergic urticaria\", \"Chromhidrosis\", \"Chromoblastomycosis\", \"Chronic actinic dermatitis\", \"Chronic bullous disease of childhood\", \"Chronic Eczema\", \"Chronic Folliculitis\", \"Chronic hand Eczema\", \"Chronic paronychia\", \"Chronic URTICARIA\", \"Chronic vesiculobullous hand dermatitis\", \"Cicatricial pemphigoid\", \"Clubbing\", \"Coccidioidomycosis\", \"Cockayne syndrome\", \"Cold sores\", \"Cold urticaria\", \"common wart \", \"Common warts\", \"Condylomata lata\", \"Congenital erythropoietic porphyria\", \"Congenital Hemangiomas\", \"Congenital ichthyosiform erythroderma\", \"cont\", \"Contact dermatitis\", \"Corn (callus)\", \"Cradle cap\", \"Cryoglobulinemic vasculitis\", \"Cutaneous fibroma\", \"Cutaneous larva migrans\", \"Cutaneous leukocytoclastic vasculitis\", \"Cutaneous T cell lymphoma (Mycosis fungoides)\", \"Cutaneous T-cell lymphoma\", \"cutis laxa\", \"Cutis Marmorata Telangiectatica Congenita\", \"Dandruff\", \"Darier (Darier–White) disease\", \"Decubitus (Pressure) Ulcers\", \"Delayed pressure urticaria\", \"Delusions of Parasitosis\", \"Dermatitis Artefacta\", \"Dermatitis herpetiformis\", \"Dermatofibroma\", \"Dermatofibrosarcoma protuberans﻿ (DFSP)\", \"Dermatomyositis\", \"Dermatosis papulosa nigra (DPN)\", \"Diaper dermatitis\", \"Diaper rash\", \"Diffuse cutaneous leishmaniasis\", \"Diffuse keratoderma\", \"Discoid lupus erythematosus\", \"Dowling-Degos disease\", \"Drug induced pigmentation\", \"Drug induced urticaria\", \"drug reaction with eosinophilia and systemic symptoms (DRESS)\", \"Drug-induced lupus erythematosus.\", \"Drug-induced nail disease\", \"Drug-induced photosensitivity\", \"Dry skin\", \"Dyschromatosis hereditaria universalis\", \"DYSCHROMATOSIS SYMMETRICA HEREDITARIA\", \"Dyshidrotic eczema﻿\", \"Dyskeratosis congenita\", \"dyskeratosis congenita\", \"Dyskeratosis congenital\", \"Dystrophic Epidermolysis Bullosa\", \"ectodermal dysplasia syndrome\", \"ecz\", \"Eczema\", \"Eczema & Abscess\", \"Ehlers-Danlos syndrome\", \"Enterovirus infections\", \"Eosinophilic folliculitis\", \"Eosinophilic granulomatosis with polyangiitis\", \"Eosinophilic pustulosis\", \"Epidermal naevus\", \"Epidermal Peeling\", \"Epidermodysplasia verruciformis\", \"Epidermoid cyst\", \"Epidermolysis bullosa﻿\", \"Epidermolysis bullosa acquisita (EBA)\", \"Epidermolysis Bullosa Simplex\", \"Epidermolytic hyperkeratosis\", \"Erosio interdigitalis blastomycetica\", \"Erysipelas\", \"Erysipeloid\", \"Erythema ab igne\", \"Erythema Annulare Centrifugum\", \"Erythema chronicum migrans\", \"Erythema elevatum diutinum\", \"Erythema gyratum repens\", \"ERYTHEMA INDURATUM\", \"Erythema infectiosum\", \"Erythema marginatum\", \"Erythema multiforme\", \"Erythema nodosum\", \"Erythema nodosum leprosum\", \"Erythema toxicum neonatorum\", \"Erythema Toxicum Neonatorum\", \"Erythrasma\", \"Erythrasma\", \"Erythrodermic psoriasis\", \"Erythromelanosis follicularis faciei et colli\", \"Erythropoietic protoporphyria\", \"Exanthematous drug eruptions (morbilliform)\", \"Exfoliative Cheilitis\", \"Exfoliative dermatitis (Erythroderma)\", \"Familial lentiginosis\", \"Fanconi anemia\", \"Feet eczema\", \"Female pattern hair loss\", \"Fibrous papule of nose\", \"Fissured tongue\", \"Fixed drug eruption\", \"focal dermal hypoplasia\", \"Focal keratoderma\", \"Follicular eczema\", \"folliculitis\", \"Folliculitis\", \"Folliculitis﻿﻿\", \"Folliculitis decalvans\", \"Fong disease\", \"Fox–Fordyce disease\", \"Freckles\", \"Frontal Fibrosing Alopecia\", \"Frontal fibrosing alopecia\", \"Gardner syndrome\", \"Generalised pustular psoriasis\", \"Generalized vitiligo\", \"Genital herpes﻿﻿﻿\", \"Genital psoriasis\", \"Genital ulcer disease syndrome\", \"Genital ulcer syndrome\", \"Genital vitiligo\", \"Genital wart (condylomata accuminata)\", \"Genital warts﻿\", \"Giant cell arteritis\", \"Glomus Tumor\", \"Goltz syndrome\", \"Goltz-Gorlin syndrome\", \"Gonorrhea\", \"Gorlin syndrome\", \"Gorlin-Goltz syndrome\", \"Graft-Versus-host Disease\", \"Gram-negative folliculitis\", \"Gram-negative folliculitis\", \"Granuloma annulare\", \"Granuloma annulare﻿\", \"Granuloma Faciale\", \"Granuloma inguinale\", \"Granulomatosis with polyangiitis\", \"Griscelli syndrome\", \"Grover disease\", \"Guttate psoriasis\", \"Habit-tic nail deformity\", \"Hailey–Hailey disease (HHD)\", \"Hair dye allergy\", \"Hair follicle tumours\", \"Hair loss﻿\", \"Hairy tongue\", \"Halo moles\", \"Hamartoma\", \"Hand eczema\", \"Hand foot and mouth disease\", \"Hand-foot-and-mouth disease﻿\", \"Head lice﻿\", \"Healig Pyoderma\", \"Healing Pyoderma PYODERMA\", \"Heart disease: Warning signs﻿ on your skin﻿\", \"Hemangioma\", \"Hepatoerythropoietic porphyria\", \"Hereditary coproporphyria\", \"Hereditary onycho-osteodysplasia\", \"Herpangina / vesicular stomatitis (oral ulcers)\", \"Herpes simplex\", \"Herpes zoster\", \"Herpus  Simplex \", \"Herpus simplex with2nd bact infec\", \"Hibernoma\", \"Hidradenitis suppurativa﻿\", \"Hidradenoma\", \"Hirsutism\", \"Histoplasmosis\", \"Hives\", \"Hutchinson-Gilford syndrome\", \"Hydroa Vacciniforme\", \"Hyperhidosis\", \"Hyperhydrosis\", \"Hyperkeratotic hand dermatitis\", \"Hypermelanosis\", \"Hypersensitivity vasculitis (palpable purpura)\", \"Hypertrichosis\", \"Hypomelanosis\", \"Hypomelanosis & Vitiligo\", \"Hypomelanosis of Ito\", \"Ich\", \"Ichthyema\", \"Ichthyosis vulgaris﻿\", \"Id reaction\", \"Idiopathic eruptive macular hyperpigmentation\", \"Idiopathic Guttate hypomelanosis\", \"Idiopathic guttate hypomelanosis\", \"IgA vasculitis (Henoch-Schönlein purpura)\", \"Impetigo\", \"Incontinentia pigmenti\", \"Infantile acropustulosis\", \"Infantile acropustulosis\", \"Infantile Hemangiomas\", \"Infected Eczema\", \"Infected wound\", \"Infectious mononucleosis\", \"Infective Eczema\", \"Inflammatory linear verrucous epidermal nevus\", \"Ingrown hair\", \"Insect  Bite \", \"Intertriginous Candidiasis\", \"Intertrigo\", \"Intrahepatic cholestasis of pregnancy\", \"Inverse lichen planus\", \"Inverse psoriasis\", \"Irritant contact dermatitis\", \"Isotretinoin induced reaction\", \"Junctional Epidermolysis Bullosa\", \"Juvenile plantar dermatosis\", \"Juvenile xanthogranuloma\", \"Kaposi’s sarcoma\", \"Kaposiform hemangioendothelioma\", \"Kawasaki disease\", \"Keloid scars\", \"kera\", \"Keratin cyst\", \"Keratinocytic epidermal nevus\", \"Keratoacanthoma\", \"Keratoderma\", \"Keratosis pilaris﻿\", \"Kimura disease\", \"Kindler Syndrome\", \"Klippel–Trenaunay syndrome\", \"Kyrle’s disease (KD)\", \"Labial melanotic macule\", \"Lamellar Ichthyosis\", \"Langerhans cell histiocytosis\", \"Large-plaque parapsoriasis (LPP)\", \"Leishmaniasis\", \"Lentigines\", \"Lentigo maligna\", \"LEOPARD Syndrome\", \"Leprosy﻿\", \"Leprosy reaction type I\", \"Leprosy reaction type II\", \"Leukoderma\", \"Leukonychia\", \"Leukoplakia\", \"Leukotrichia\", \"Lichen nitidus\", \"Lichen pilano pilaris\", \"Lichen planus﻿\", \"Lichen planus – lip\", \"Lichen planus – nail\", \"Lichen Planus & Atopic Diathesis\", \"Lichen planus nail\", \"Lichen planus pemphigoides\", \"Lichen planus pigmentosus\", \"lichen s\", \"Lichen sclerosus et atrophicus\", \"Lichen simplex chronicus\", \"Lichen striatus\", \"Lichenoid keratosis\", \"Lichenoid reaction\", \"Linea nigra\", \"Linear and whorled nevoid hypermelanosis\", \"Linear IgA disease\", \"Linear porokeratosis\", \"Lipodermatosclerosis\", \"Lipodystrophy\", \"Lipoma\", \"Localized cutaneous Leishmaniasis\", \"Lower abdominal pain syndrome\", \"Lupus erythematosus panniculitis\", \"Lupus erythematosus profundus\", \"Lupus neonatorum\", \"Lupus tumidus\", \"Lyme disease\", \"Lympha\", \"Lymphangioma\", \"Lymphedema\", \"Lymphedema with Lymphangitis\", \"Marfan syndrome\", \"Mastocytosis\", \"Measles\", \"Melanocytic nevus\", \"Melanoma\", \"Melanonychia\", \"melasfgdgma\", \"Melasma\", \"Melasma & Tinea Ungum\", \"Melasma& Rosacea\", \"Melasma,DPN Skin Tags\", \"Melkersson–Rosenthal syndrome\", \"Merkel cell carcinoma﻿﻿\", \"Microscopic polyangiitis\", \"Migratory glossitis (Geographic tongue)\", \"Mild Podoconosis & Tinea Pedis\", \"Mild Rosacea\", \"Milia\", \"Miliaria\", \"Milker\'s nodules\", \"Mixed vitiligo\", \"Moles\", \"Molluscum contagiosum\", \"Mongolian spot\", \"Monilethrix (beaded hair)\", \"Monkeypox\", \"Monkeypox rash\", \"Morphea\", \"Mucocutaneous Leishmaniasis\", \"Mucormycosis\", \"Mucosal vitiligo\", \"Mucous membrane pemphigoid\", \"Mucous/myxoid cyst\", \"Mycetoma\", \"Mycoplasma genitalium\", \"Myiasis\", \"Nail fungus\", \"Nail Plate Dystrophy\", \"Nail psoriasis\", \"Nail-patella syndrome\", \"Necrobiosis lipoidica\", \"Necrolytic migratory erythema\", \"Neonatal acne\", \"Neurodermatitis\", \"Neurofibroma (solitary)\", \"Neurofibromatosis\", \"Neutrophilic eccrine hidradenitis\", \"Nevus comedonicus\", \"Nevus of Hori\", \"Nevus of Ito\", \"Nevus of Ota\", \"Nevus sebaceous\", \"Nevus simplex (salmon patch)\", \"Nickel allergy\", \"Nickel allergy﻿\", \"nodular\", \"Nummular dermatitis﻿\", \"Nummular Eczema\", \"Ochronosis\", \"Ocular rosacea﻿\", \"Onchocerciasis\", \"Onychocryptosis (ingrown nails)\", \"Onycholysis\", \"Onychomadesis\", \"Onychomatricoma\", \"Onychomycosis / tinea unguium\", \"Onychopapilloma\", \"Onychophagia\", \"Onycomycosis\", \"Onycomycosis & Post Inflammatory Hyperpigmentation\", \"Oral candidiasis\", \"Oral candidiasis\", \"Oral erythroplakia\", \"Oral hairy leukoplakia.\", \"Orf\", \"Orofacial granulomatosis (granulomatous cheilitis)\", \"Osteogenesis imperfecta\", \"Pachydermatous eosinophilic dermatitis\", \"Pachydermoperiostosis\", \"Pachyonychia congenital\", \"Paget disease\", \"Paget’s Disease\", \"Panniculitis\", \"Papular acrodermatitis of childhood (Gianotti-Crosti syndrome)\", \"Papular urticaria\", \"Parakeratosis pustulosa\", \"Paraneoplastic pemphigus\", \"Paraphimosis\", \"Parkes Weber syndrome\", \"Paronychia\", \"Pattern hair loss\", \"Pcychosis Barbe\", \"Pearly penile papule\", \"Pediculosis capitis (head lice)\", \"Pediculosis corporis\", \"Pemphigoid gestationis\", \"Pemphigus﻿\", \"Pemphigus foliaceus\", \"Pemphigus vulgaris\", \"Perforating folliculitis (PF)\", \"Perioral Dermatitis\", \"Perioral dermatitis﻿\", \"PERIORBITAL HYPERPIGMENTATION\", \"Peutz–Jeghers Syndrome\", \"Peutz-Jeghers syndrome (PJS)\", \"Phacomatosis\", \"Phimosis\", \"photo\", \"photo Allergic Contact Dermatitis\", \"Photoallergy\", \"Photosensitivity\", \"Phototoxicity\", \"PHYTOPHOTODERMATITIS\", \"Piebaldism\", \"Pilar cyst\", \"Pilomatricoma\", \"Pilomatrix carcinoma\", \"Pitiriyasis Alba\", \"Pitiriyasis Rosea\", \"Pitriasis Alba & Atopic Eczema\", \"Pitted Keratolysis\", \"Pityriasis alba\", \"Pityriasis amiantacea\", \"Pityriasis folliculorum\", \"Pityriasis lichenoides chronica (PLC)\", \"Pityriasis lichenoides et varioliformis acuta (PLEVA)\", \"Pityriasis rosea\", \"Pityriasis rotunda\", \"Pityriasis rubra pilaris\", \"Pityriasis versicolor\", \"Pityrosporum folliculitis\", \"Plantar wart\", \"Polyarteritis nodosa (PAN)\", \"Polymorphic light eruption\", \"Pompholyx\", \"Porokeratosis\", \"Poroma\", \"Porphyria\", \"Porphyria cutanea tarda\", \"Port-wine stains\", \"Post Acne Hyperpigmentation\", \"Post Acne Ice box Scar\", \"Post Burn Scar & Contracture\", \"Post burn scar removal \", \"Post Herpes scars\", \"Post herpetic neuralgia\", \"Post inflamatory HYPERPIMENTATION\", \"Post inflammatory hyperpigmentation\", \"Post inflammatory hypopigmentation\", \"Post-kala-azar dermal Leishmaniasis\", \"Premature aging\", \"Premature greying of hair\", \"Progeria\", \"PROGRESSIVE MACULAR HYPOMELANOSIS\", \"Proteus syndrome\", \"Proteus syndrome\", \"Prurigo nodularis\", \"Prurigo nodularis\", \"Pruritic urticarial papules and plaques of pregnancy\", \"Pruritic urticarial papules and plaques of pregnancy\", \"Pruritis & Pregnancy\", \"Pseudoainhum\", \"Pseudoainhum\", \"Pseudofolliculitis barbae\", \"Pseudofolliculitis barbae\", \"Pseudomonas folliculitis\", \"Pseudomonas folliculitis\", \"Pseudoxanthoma elasticum\", \"Pseudoxanthoma elasticum\", \"Psoriasis\", \"Psoriasis\", \"Psoriatic arthritis\", \"Psoriatic arthritis\", \"Psoriatic nail disease\", \"Psoriatic nail disease\", \"Pubic lice\", \"Pubic lice\", \"Punctate keratoderma\", \"Punctate keratoderma\", \"Pustular psoriasis\", \"Pustular psoriasis\", \"Pustular psoriasis of pregnancy\", \"Pustular psoriasis of pregnancy\", \"PV & SD\", \"Pyoderma gangrenosum\", \"Pyoderma gangrenosum\", \"Pyogenic granuloma\", \"Pyogenic granuloma\", \"qwe\", \"Raynaud\'s disease\", \"Recurrent folliculitis\", \"Reticulate acropigmentation of Dohi\", \"Retronychia\", \"Rhinophyma\", \"Riehl melanosis\", \"Ringworm\", \"Rosa\", \"Rosacea\", \"Rosacea &Melasma\", \"Rosasea\", \"Roseola (erythema subitum)\", \"Rothmund-Thomson syndrome\", \"Sarcoidosis\", \"Scabies\", \"Scabies\", \"Scalp psoriasis\", \"Scalp psoriasis\", \"scar\", \"Scarring Alopecia\", \"Scars\", \"Sclerema neonatorum\", \"Scleroderma\", \"Scrotal calcinosis\", \"SEB\", \"seb\", \"Seb\", \"Seb\", \"Sebaceous carcinoma\", \"Sebaceous Hyperplasia\", \"Sebo\", \"seboatopic dermatitis\", \"Sebo-psoriasis\", \"sebor\", \"Seborrhoeic dermatitis\", \"Seborrhoeic keratosis\", \"Segmental vitiligo\", \"Senile Pruritis\", \"SENILE PRURITIS\", \"Shoe dermatitis / allergy\", \"Skin cancer\", \"Skin care\", \"Skin care\", \"Skin tags\", \"Small vessel vasculitis\", \"Small-plaque parapsoriasis (SPP)\", \"Solar keratoses\", \"Solar urticaria\", \"Spitz nevus\", \"Sporotrichosis\", \"Squamous cell carcinoma\", \"Squamous cell papilloma\", \"Staphylococcal Scalded Skin Syndrome\", \"Stasis Dermatitis\", \"Stasis dermatitis\", \"Steatocystoma multiplex\", \"Steroid induced Acne Rosacea\", \"Steroid Induced Rosacea\", \"Steroid Induced Rosacea\", \"Stevens-Johnson syndrome\", \"Stretch marks\", \"Striae (stretch mark)\", \"Striate keratoderma\", \"Sturge–Weber syndrome\", \"Subacute cutaneous lupus\", \"Subcorneal Pustular Dermatosis (Sneddon–Wilkinson Disease)\", \"Subcutaneous Fat Necrosis\", \"Subungual exostosis\", \"Subungual haemorrhage/haematoma\", \"Subungual melanoma\", \"Sucking Blisters\", \"Sycosis barbae\", \"Syphilis\", \"Syringoma\", \"Systemic Lupus Erythematosus\", \"Takayasu arteritis\", \"Telogen Effluvium\", \"Tinea\", \"Tinea  Corporis& Melasma\", \"tinea c\", \"Tinea Capitis\", \"Tinea capitis\", \"Tinea Capitis & Atopic Eczema\", \"Tinea corporis\", \"Tinea cruris\", \"Tinea faciei\", \"Tinea incognito\", \"Tinea manuum\", \"Tinea pedis\", \"Tinea Pedis & Bromhydrosis\", \"Tinea pedis /athlete\'s foot\", \"Tinea ungum\", \"Tinea versicolor\", \"Tip Vitiligo\", \"Tonsilitis\", \"Touraine-Solente-Golé syndrome\", \"Toxic epidermal necrolysis\", \"Trachyonychia\", \"Traction Alopecia\", \"Transient Neonatal Pustular Melanosis\", \"Transient pustular melanosis\", \"Triangular Temporal Alopecia\", \"Trichoepithelioma\", \"Trichomoniasis\", \"Trichomycosis\", \"Trichorrhexis invaginata\", \"Tricotillomania\", \"Tuberous sclerosis complex (TSC)\", \"Tufted angioma\", \"Tungiasis\", \"Twenty nail dystrophy\", \"Urethral discharge syndrome\", \"Urticaria – acute\", \"Urticaria – chronic\", \"Vaginal discharge syndrome\", \"Varicella Zoster \", \"Variegate porphyria\", \"Verruca Plana\", \"Veruca Vulgaris with 2nd bact infec\", \"Vesicular Palmoplantar eczema\", \"Vibratory urticaria\", \"Vitiligo\", \"Vulvodynia\", \"Vulvo-vaginal candidiasis\", \"Waardenburg Syndrome\", \"Wart Mucosal\", \"Warts\", \"Wells syndrome (eosinophilic cellulitis)\", \"Werner syndrome\", \"Wrinkles\", \"Wrinkling\", \"Xeroderma pigmentosum\", \"X-linked ichthyosis\", \"Yellow nail syndrome\", \"Xerotic face\", \"Steroid dependent acne \", \"Exfoliative hand dermatitis \", \"Blister 20 to cryo\", \"1st degree Burn\", \"Post scabetic pruritus \", \"Paederus dermatitis\", \"Bacterial dactylitis\", \"Viral exanthem \", \"Ingrown nail\", \"Generalized psoriasis\", \"Generalized atopic eczema\", \"Alopecia localised\", \"Atopic eczema infantile\", \"Lichen planus generalised\", \"Seborroic dermatities\", \"Steroid dependent skin\", \"Hand foot mouth disease\", \"Drug reaction generalized\", \"Facial eczema\", \"Photo dermatities\", \"\", \"Pityriasis versicolar\", \"Post inflammatory hyperpigmentation (PIH)\", \"Acne vulgaris\", \"Steroid induced acne\", \"Pigmented pupuric dermatosis\", \"Tinea capitis with secondary bacterial infection\", \"Hypertrophied lichen planus\", \"Cheilitis\", \"Candidiasis\", \"Photo dermatitis\", \"Acne cystic\", \"R Arthritis \", \"Generalized xerosis \", \"Flexural eczema\", \"Pruritus vulva\", \"Deep fungal infection\", \"Hyperhidrosis\", \"Tattoo removal\", \"Burn injury \", \"Xerotic skin\", \"Cystic acne\", \"Actinomycosis \", \"Lichenoid eruption\", \"Acrochordons\", \"Perianal pruritus\", \"External haemorroides\", \"Balanitis \", \"Acneforme eruption \", \"Leg Eczema\", \"Cutaneous leishmaniasis\", \"Generalized Vitiligo\", \"Ecthyma\", \"Drug reaction\", \"Furuncle\", \"Sensitization Eczema\", \"Scrotal swelling \", \"Actinic lichen planus\", \"Diabetic neuropathy\", \"Secondary pruritus \", \"Acute sun burn \", \"Photo ICD\", \"Geographic tongue\", \"Democideosis\", \"Generalized lichen planus\", \"Notalgia paresthetica\", \"Soft tissue tumor / fibroma\", \"Nevus anemicus\", \"Urticarial vasculitis\", \"Urticarial vasculities\", \"Allergic hand dermatitis \", \"Chilblains\", \"Visible pore on face\", \"Visible pores on face\", \"Periorbital hyperpigmentation\", \"Pruritus\", \"Mycosis fungoides\", \"Skin wrinkle\", \"Laser hair removal\", \"Chronic plaue psoriasis\", \"Chronic plaque psoriasis\", \"Generalized eczema\", \"Secondary bacterial infection\", \"Diffuse alopecia\", \"Angry psoriasis\", \"Erytherodermic psoriasis\", \"Palmar psoriasis\", \"Drug reaction \", \"Varicose eczema\", \"Palmo Plantar Psoriasis\", \"Neuropathy\", \"Staphylococcal scalded skin syndrome\", \"Seborrhea \", \"Foot mouth hand disease\", \"Eyelid eczema\", \"Urinary tract infection\", \"UTI\", \"Ecchymosis secondary to ?\", \"Generalized Eczema\", \"Hypertrophied scar\", \"Palmo plantar hyperkeratosis\", \"Plantar hyperkeratosis\", \"Hyperkeratosis plantar\", \"Hemorrhoids\", \"Erythema Multiforme\", \"Sebaceous Nevus\", \"Nipple eczema\", \"Bullous Disease\", \"Diabetes Mellitus\", \"Pregnancy\", \"Sebaceous adenoma\", \"Erytheroderma\", \"Post burn depigmentation \", \"Nevus flammeus\", \"Atopic Eczema with Secondary Bacterial infection\", \"Acute eczema\", \"Sebo psoriasis \", \"Striae \", \"Breast Eczema\", \"Keratinization disorder\", \"Pruritus ani\", \"Foot Eczema\", \"Finger tip eczema\", \"Umbilical lesion\", \"Chronic pruritus\", \"Mucocele\", \"Scalp Seborrheic dermatitis\", \"Acquired ichthyosis\", \"Hanging nail\", \"Linear Eczema\", \"Wart\", \"Perianal Fistula\", \"Verrucous nevus\"]', NULL, '2024-08-19 13:28:04', '2025-02-10 12:57:38', NULL),
(2, 'chief_complaints', '[\"Itching\", \"Rash\", \"Pain\", \"Bleeding\", \"Discharge\", \"Blistering\", \"Abnormal pigmentation\", \"Dry skin\", \"Open sores\", \"Lesions\", \"Ulcers\", \"Bumps\", \"Peeling skin\", \"Scaly\", \"Rough skin\", \"Scalp lesion of\", \"Skin lesion of \", \"Genital lesion of \", \"Face lesion of \", \"Came for follow up \", \"Sent for biopsy \"]', NULL, '2024-08-19 13:29:03', '2024-12-14 09:37:28', NULL),
(3, 'hpi', '[\"Hpi test\", \"Whitish discoloration \", \"Recurrent\", \"Itchy\"]', NULL, '2024-08-19 14:11:19', '2024-11-30 12:54:38', NULL),
(4, 'past_medical_history', '[\"Past medical history test\", \"Post inflammatory hypopigmentation\"]', NULL, '2024-08-19 14:12:20', '2024-11-22 11:24:57', NULL),
(5, 'region', '[\"Addis Ababa\", \"Afar Region\", \"Amhara Region\", \"Benishangul-Gumuz\", \"Central Ethiopia\", \"Dire Dawa\", \"Gambela\", \"Harari\", \"Oromia\", \"Sidama\", \"Somali\", \"South Ethiopia\", \"South West Ethiopia\", \"Tigray Region\"]', NULL, '2024-10-01 21:25:58', '2024-10-01 21:25:58', NULL),
(6, 'subcity', '[\"Addis Ketema\", \"Akaki Kaliti\", \"Arada \", \"Bole\", \"Gullele\", \"Kirkos\", \"Kolfe Keranio\", \"Lideta \", \"Nifas Silk Lafto\", \"Yeka\", \"Lemi Kura\", \"Burayu\", \"Eka Tafo\", \"Furi\", \"Gefersa Guji\", \"Gelan\", \"Gelan Guda\", \"Koye\", \"Kara Gida\", \"Mana Abichu\", \"Melka Nono\", \"Sebeta\", \"Sululta\"]', NULL, '2024-10-01 21:25:58', '2024-10-01 21:25:58', NULL),
(7, 'allergy', '[\"Amox\"]', NULL, '2024-10-09 17:30:06', '2024-10-09 17:30:06', NULL),
(8, 'drug', '[\"Test\", \"Cholestrole\"]', NULL, '2024-10-09 17:30:27', '2024-11-06 10:36:19', NULL),
(9, 'disease', '[\"ART\", \"Cholestrone\"]', NULL, '2024-10-14 12:01:29', '2024-11-06 10:34:54', NULL),
(10, 'physical_finding', '[\"Depigmented patches \", \"Hypopigmented patches\", \"Papules, pigmented macules and scars \", \"Lichenified plaque \", \"Crusted plaque\", \"Warty plaques\", \"Scaly plaque\", \"Pigmented patches \"]', NULL, '2024-10-14 12:01:29', '2024-11-30 12:17:32', NULL),
(11, 'compounding_remarks', '[\"1ኛ ሳምንት በየቀኑ, 2ኛ ሳምንት 1 ቀን እየታለፈ, 3ኛ ሳምንት 2 ቀን እየታለፈ, 4ኛ ሳምንት 3 ቀን እየታለፈ\", \"ለ 5 ቀን ጠዋት እና ማታ ሻወር ሳይወሰድ, ከዚያ በኃላ ለ7 ቀን ማታ ብቻ\", \"ማታ ማታ ችግሩ ያለበት ቦታ በስሱ መቀባት\", \"ጠዋት ጠዋት ችግሩ ያለበት ቦታ በስሱ መቀባት\", \"ችግሩ ያለበት ቦታ ጠዋት ተቀብቶ ማታ መታጠብ\", \"ችግሩ ያለበት ቦታ ማታ ተቀብቶ ጠዋት መታጠብ\", \"1ኛ አና 2ኛ ሳምንት በየቀኑ, 3ኛ እና 4ኛ ሳምንት 1 ቀን እየታለፈ, 5ኛ እና 6ኛ ሳምንት 2 ቀን እየታለፈ, 7ኛ እና 8ኛሳምንት 3 ቀን እየታለፈ\", \"1ኛ ወር በየቀኑ,2ኛ ወር 1 ቀን እየታለፈ\", \"ለ 1 ሳምንት 2 ቀን እየታለፈ, 1ኛ ወር 1 ቀን እየታለፈ, 2ኛ ወር በየቀኑ\", \"1ኛ ሳምንት 2 ቀን እየታለፈ, 2ኛ እና 3ኛ ሳምንት 3 ቀን እየታለፈ, ከዚያ በኃላ በሳምንት 1 ጊዜ\", \"1ኛ እና 2ኛ ሳምንት 3 ቀን እየታለፈ, ከዚያ በኃላ በሳምንት 1 ጊዜ\", \"ለ ራስ ቅል\", \"ብብት አከባቢ ላይ በሰሱ መቀባት\", \"ለ ሰውነት\", \"ለ ብሽሽት\", \"1ኛ  ሳምንት በየቀኑ, 2ኛ ሳምንት 1 ቀን እየታለፈ, 3ኛ እና 4ኛ ሳምንት 2 ቀን እየታለፈ, 5ኛ እና 6ኛሳምንት 3 ቀን እየታለፈ ለ ቀጣይ በ ሳምንት 1 ጊዜ\", \"ለ አንገት\", \"ለ ሙሉ ፊት\", \"ለፊት ሁል  ግዜ ማታ በስሱ መቀባት ፣ለሰውነት ከታጠቡ በሁላ በስሱ መቀባት\", \"ለፊት ለ1ሳምንት በየቀኑለ\", \"ለፊት ለ1ሳምንት በየቀኑለ ማታ  ማታ በስሱ መቀባት\", \"ለ  ፊት\", \"ለ ቡጉሩ ቦታ ማታ በስሱ መቀባት\", \"ለ ጠቆረው ቦታ ማታ በስሱ መቀባት\", \"\", \"በ እርጥብ ቆዳ መቀባት\", \"ጠዋት እና ማታ ችግሩ ያለበት ቦታ በስሱ መቀባት ለ 1 ሳምንት\", \"ለ እግር\", \"ከ 1 ሳምንት በኋላ ሚጀመር\", \"ጠዋት እና ማታ ችግሩ ያለበት ቦታ በስሱ መቀባት\", \"1ኛ ሳምንት በየቀኑ, 2ኛ ሳምንት 1 ቀን እየታለፈ, 3ኛ ሳምንት 2 ቀን እየታለፈ, 4ኛ ሳምንት 3 ቀን እየታለፈ ችግሩ ያለበት ቦታ ለይ በስሱ መቀባት\", \"በሳምንት 3 ጊዜ ለ ሰውነትበ እርጥብ ቆዳ መቀባት\", \"ለ ፈት እና ለ እጅ በ እርጥብ ቆዳ መቀባትለ  ለ1ሳምንት በየቀኑለ ማታ  ማታ በስሱ መቀባትከ\", \"ለ1ሳምንት በየቀኑ  ከ አንገት በታች ሙሉ ሰውነት መቀባት ማታ ተቀብቶ ጠዋት መታጠብ\", \"ለ እጅ\", \"ለፊት በየቀኑ ማታ ማታ በእርጥብ ቆዳ \\nለሰውንት ከሻወር በኋላ\", \"ጠዋት እና ማታ ለ 5 ቀን\", \"ከተዘፈዘፉ በኋላ በየቀኑ ማታ በ እርጥብ ቆዳ መቀባት\", \"ከ 1 ሳምንት በኃላ ይጀመር\"]', NULL, '2024-10-01 21:25:58', '2025-01-30 11:56:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_bills`
--

CREATE TABLE `hospital_bills` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `order_id` text NOT NULL,
  `billed_date` datetime NOT NULL DEFAULT current_timestamp(),
  `bill_type` varchar(6) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `company_id` int(11) DEFAULT 0,
  `company_name` varchar(50) DEFAULT '',
  `bill_status` int(11) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `payment_request_id` int(11) DEFAULT NULL,
  `pos_bill_id` int(11) NOT NULL DEFAULT 0,
  `sent_to_pos` tinyint(1) NOT NULL DEFAULT 0,
  `fs_number` varchar(10) DEFAULT NULL,
  `payed_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_bill_details`
--

CREATE TABLE `hospital_bill_details` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `service_type` varchar(20) NOT NULL,
  `bill_quantity` float NOT NULL,
  `bill_amount` float NOT NULL,
  `bill_discount` int(11) NOT NULL,
  `bill_date` datetime NOT NULL DEFAULT current_timestamp(),
  `service_status` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_bill_payments`
--

CREATE TABLE `hospital_bill_payments` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payed_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payed_amount` float NOT NULL,
  `payed_discount` float NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `tin` varchar(13) NOT NULL,
  `fs_number` varchar(10) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `payment_type` varchar(20) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `due` float NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_biopsy_result`
--

CREATE TABLE `hospital_biopsy_result` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `sex` varchar(6) NOT NULL,
  `age` varchar(50) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `biopsy_result` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`biopsy_result`)),
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `micrscopic_image` text DEFAULT NULL,
  `revision` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_chief_complaints`
--

CREATE TABLE `hospital_chief_complaints` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_company`
--

CREATE TABLE `hospital_company` (
  `id` int(11) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `tin_number` varchar(13) NOT NULL,
  `description` varchar(50) NOT NULL,
  `is_active` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `registered_by_name` varchar(50) NOT NULL,
  `unpaid_visit_days` int(11) NOT NULL,
  `is_cash` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_compounding_prescription_detail`
--

CREATE TABLE `hospital_compounding_prescription_detail` (
  `id` int(11) NOT NULL,
  `main_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`item_detail`)),
  `amount` decimal(10,0) NOT NULL,
  `measurement` varchar(10) NOT NULL,
  `in_percent` decimal(10,0) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_compounding_prescription_main`
--

CREATE TABLE `hospital_compounding_prescription_main` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `prescription_group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `patient_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`patient_detail`)),
  `created_by` int(11) NOT NULL,
  `created_by_name` varchar(100) NOT NULL,
  `is_sold` tinyint(1) NOT NULL DEFAULT 0,
  `is_void` int(11) NOT NULL DEFAULT 0,
  `route` varchar(20) NOT NULL,
  `direction` varchar(20) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `external_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`external_detail`)),
  `company_id` int(11) NOT NULL DEFAULT 0,
  `company_name` varchar(100) DEFAULT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_compounding_templet`
--

CREATE TABLE `hospital_compounding_templet` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `compounding_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`compounding_list`)),
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_derma_medical_history`
--

CREATE TABLE `hospital_derma_medical_history` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_diagnoses`
--

CREATE TABLE `hospital_diagnoses` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_diseases`
--

CREATE TABLE `hospital_diseases` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_drugs`
--

CREATE TABLE `hospital_drugs` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_examination_extra_oral`
--

CREATE TABLE `hospital_examination_extra_oral` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_examination_intra_oral`
--

CREATE TABLE `hospital_examination_intra_oral` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_external_documents`
--

CREATE TABLE `hospital_external_documents` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `requested_company_name` varchar(50) NOT NULL,
  `request_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `status` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_follow_up`
--

CREATE TABLE `hospital_follow_up` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `selected_skin` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`selected_skin`)),
  `remark` text NOT NULL,
  `selected_teeth` varchar(500) NOT NULL,
  `treatment_plans` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`treatment_plans`)),
  `date_created` date NOT NULL,
  `temp_visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_follow_up`
--

INSERT INTO `hospital_follow_up` (`id`, `patient_id`, `visit_id`, `patient_name`, `mobile`, `company_id`, `company_name`, `selected_skin`, `remark`, `selected_teeth`, `treatment_plans`, `date_created`, `temp_visit_id`, `doctor_id`, `doctor_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Mukera And Hulet', '0913144095', 0, '', '{\"full_body\":[[59,89],[69,135],[61,83],[93,96],[86,143],[71,183],[80,221],[45,151],[52,241],[46,276],[66,201]],\"face_only\":[],\"default_image_type\":\"full_body\"}', '', '', NULL, '2026-07-21', 0, 1, 'admin', '2026-07-21 22:30:09', '2026-08-10 17:54:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_laboratory_results`
--

CREATE TABLE `hospital_laboratory_results` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_detail_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `result` varchar(1000) NOT NULL,
  `reference_range` varchar(100) NOT NULL,
  `result_flag` varchar(10) DEFAULT NULL,
  `registered_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_laboratory_result_attributes`
--

CREATE TABLE `hospital_laboratory_result_attributes` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `test_name` varchar(50) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `reference_range_from` varchar(50) NOT NULL,
  `reference_range_to` varchar(50) NOT NULL,
  `reference_range_gender` varchar(6) NOT NULL,
  `reference_range_unit` varchar(50) NOT NULL,
  `quantifiable` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_lab_requests`
--

CREATE TABLE `hospital_lab_requests` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `company_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `upper_tray` int(11) NOT NULL,
  `lower_tray` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `special_wish` varchar(50) NOT NULL,
  `date_impression` datetime NOT NULL DEFAULT current_timestamp(),
  `delivery_date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `teeth_selected` text NOT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `paid_amount` float DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_medical_certificate`
--

CREATE TABLE `hospital_medical_certificate` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `treatment_given` varchar(200) NOT NULL,
  `diagnosis` varchar(200) NOT NULL,
  `registerd_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `rest_days` varchar(200) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `visit_dates` text DEFAULT NULL,
  `next_appointment` varchar(100) DEFAULT NULL,
  `regisered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `recommendation` varchar(200) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `reference` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_medical_history`
--

CREATE TABLE `hospital_medical_history` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_medical_report`
--

CREATE TABLE `hospital_medical_report` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `sex` varchar(6) NOT NULL,
  `age` varchar(50) NOT NULL,
  `mobile` varchar(10) NOT NULL,
  `history` text NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_message`
--

CREATE TABLE `hospital_message` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `message` varchar(200) NOT NULL,
  `link` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_nurse_history`
--

CREATE TABLE `hospital_nurse_history` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `chief_complaints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chief_complaints`)),
  `past_medical_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`past_medical_history`)),
  `hpi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hpi`)),
  `diagnosis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`diagnosis`)),
  `medication` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medication`)),
  `procedure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`procedure`)),
  `registered_date` datetime NOT NULL,
  `post_procedure_diagnosis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`post_procedure_diagnosis`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_order_detail`
--

CREATE TABLE `hospital_order_detail` (
  `id` int(11) NOT NULL,
  `order_main_id` int(11) NOT NULL,
  `ordered_service_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `order_comment` text NOT NULL,
  `bill_detail_id` int(11) NOT NULL,
  `sample_taken` int(11) NOT NULL,
  `sample_taken_date` datetime NOT NULL,
  `sample_type` varchar(50) NOT NULL,
  `sample_taken_from` varchar(50) NOT NULL,
  `sample_taken_by` int(11) NOT NULL,
  `result_registered` int(11) NOT NULL,
  `result_date` datetime NOT NULL,
  `result_registered_by` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_order_main`
--

CREATE TABLE `hospital_order_main` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `sex` varchar(6) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `ordered_by` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `order_service_type` varchar(50) NOT NULL,
  `is_external` int(11) NOT NULL,
  `order_status` int(11) NOT NULL,
  `sample_collected_date` datetime DEFAULT NULL,
  `sample_collected_by` int(11) DEFAULT NULL,
  `sample_sent_date` datetime DEFAULT NULL,
  `sample_sent_by` int(11) DEFAULT NULL,
  `slide_recieved_date` datetime DEFAULT NULL,
  `slide_recieved_by` int(11) DEFAULT NULL,
  `additional_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_info`)),
  `result_status` int(11) NOT NULL,
  `order_comment` text NOT NULL,
  `result_remark` text DEFAULT NULL,
  `result_approved_by` int(11) NOT NULL,
  `result_approved_date` datetime NOT NULL,
  `registered_by` int(11) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `scanned_documents` text NOT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_order_options`
--

CREATE TABLE `hospital_order_options` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(200) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(200) NOT NULL,
  `registered_date` datetime NOT NULL,
  `options` text NOT NULL COMMENT 'List of services in different options. Format: JSON\r\nExample:\r\n[\r\noption_1:{[service_id:1, quantity:2, description:"13 and 14 gum"]},\r\noption_2:{[service_id:1, quantity:3, description:""]},\r\n]',
  `status` varchar(10) DEFAULT NULL COMMENT 'Tells which plan is billed. Null is pending. (Show in pending bills)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_order_status`
--

CREATE TABLE `hospital_order_status` (
  `id` int(11) NOT NULL,
  `billdetail_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_type` int(11) NOT NULL,
  `status_type` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `status_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `registered_by` int(11) NOT NULL,
  `registred_by_name` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_patients`
--

CREATE TABLE `hospital_patients` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `sex` varchar(6) NOT NULL,
  `address` varchar(50) NOT NULL,
  `subcity` varchar(50) NOT NULL,
  `woreda` varchar(20) NOT NULL,
  `house_no` varchar(20) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `emergency_contact_name` varchar(50) NOT NULL,
  `emergency_mobile` varchar(13) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) NOT NULL,
  `company_name` varchar(50) DEFAULT NULL,
  `blood_pressure` varchar(3) NOT NULL,
  `diabetics` varchar(3) NOT NULL,
  `bleeding_tendency` varchar(3) NOT NULL,
  `drugs_taking` text DEFAULT NULL,
  `allergy` text DEFAULT NULL,
  `pregnancy` varchar(3) NOT NULL,
  `blood_group` varchar(20) NOT NULL,
  `cardiac_problem` varchar(3) NOT NULL,
  `other_diseases` text DEFAULT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `old_id` int(11) DEFAULT NULL,
  `card_location` varchar(10) DEFAULT NULL,
  `discarded_by` int(11) DEFAULT NULL,
  `attached_old_card` text NOT NULL,
  `survey` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `hospital_patients`
--

INSERT INTO `hospital_patients` (`id`, `first_name`, `middle_name`, `last_name`, `date_of_birth`, `sex`, `address`, `subcity`, `woreda`, `house_no`, `mobile`, `emergency_contact_name`, `emergency_mobile`, `registered_date`, `company_id`, `company_name`, `blood_pressure`, `diabetics`, `bleeding_tendency`, `drugs_taking`, `allergy`, `pregnancy`, `blood_group`, `cardiac_problem`, `other_diseases`, `registered_by`, `registered_by_name`, `old_id`, `card_location`, `discarded_by`, `attached_old_card`, `survey`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Mukera', 'And', 'Hulet', '1988-07-05', 'Male', 'Addis Ababa', 'Arada', '06', 'JSC', '0913144095', 'Mukera ', '0913144095', '2026-07-21 20:01:42', 0, '', 'No', 'No', 'No', NULL, NULL, 'No', '', 'No', NULL, 1, 'admin', NULL, '', NULL, '', 'Other Hospital', '2026-07-21 22:01:42', '2026-07-21 22:06:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_payment_request`
--

CREATE TABLE `hospital_payment_request` (
  `id` int(11) NOT NULL,
  `department` varchar(10) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_title` varchar(100) NOT NULL,
  `month_name` varchar(100) NOT NULL,
  `bill_id_list` text NOT NULL,
  `total_amount` float NOT NULL,
  `status` varchar(20) NOT NULL COMMENT 'Collected, Requested',
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remark` varchar(200) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_petty_cash`
--

CREATE TABLE `hospital_petty_cash` (
  `id` int(11) NOT NULL,
  `petty_cash_amount` float NOT NULL,
  `expence_from` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `expence_amont` float NOT NULL,
  `expence_reason` varchar(100) NOT NULL,
  `regisered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL,
  `payment_type` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_prescriptions`
--

CREATE TABLE `hospital_prescriptions` (
  `id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `prescription_group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `mobile` varchar(13) NOT NULL,
  `is_custom` int(11) NOT NULL DEFAULT 0,
  `is_sold` tinyint(1) NOT NULL DEFAULT 0,
  `is_void` int(11) NOT NULL DEFAULT 0,
  `company_id` int(11) DEFAULT 0,
  `company_name` varchar(100) DEFAULT NULL,
  `hide` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_prescription_auto_fill`
--

CREATE TABLE `hospital_prescription_auto_fill` (
  `id` int(11) NOT NULL,
  `drug_id` int(11) NOT NULL,
  `drug_name` varchar(200) NOT NULL,
  `age_from` int(11) NOT NULL,
  `age_to` int(11) NOT NULL,
  `remark` varchar(500) DEFAULT NULL,
  `route` varchar(20) NOT NULL,
  `direction` varchar(20) NOT NULL,
  `nod` int(11) NOT NULL,
  `registered_by` int(11) DEFAULT 0,
  `deleted_by` int(11) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_prescription_detail`
--

CREATE TABLE `hospital_prescription_detail` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `drug_id` int(11) NOT NULL,
  `drug_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`drug_detail`)),
  `drug_name` varchar(50) NOT NULL,
  `dose` varchar(30) NOT NULL,
  `route` varchar(30) NOT NULL,
  `direction` varchar(30) NOT NULL,
  `number_of_days` int(11) NOT NULL,
  `custom_direction` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_pricelist`
--

CREATE TABLE `hospital_pricelist` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `description` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `company_name` varchar(50) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_pricelist_detail`
--

CREATE TABLE `hospital_pricelist_detail` (
  `id` int(11) NOT NULL,
  `pricelist_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `service_price` float NOT NULL,
  `updated_date` datetime NOT NULL DEFAULT current_timestamp(),
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_pricelist_detail`
--

INSERT INTO `hospital_pricelist_detail` (`id`, `pricelist_id`, `service_id`, `service_name`, `service_type`, `service_price`, `updated_date`, `registered_by`, `registered_by_name`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Consultation', 'Consultation', 1000, '2026-07-21 20:03:39', 1, 'admin', 1, '2026-07-21 22:03:39', '2026-07-21 22:03:44', '2026-07-21 22:03:44'),
(2, 1, 1, 'Consultation', 'Consultation', 1000, '2026-07-21 20:03:44', 1, 'admin', 1, '2026-07-21 22:03:44', '2026-07-21 22:03:44', NULL),
(3, 1, 2, 'Minor Surgery', 'Procedure', 10000, '2026-07-21 20:27:37', 1, 'admin', 1, '2026-07-21 22:27:37', '2026-07-21 22:27:52', '2026-07-21 22:27:52'),
(4, 1, 2, 'Minor Surgery', 'Procedure', 10000, '2026-07-21 20:27:52', 1, 'admin', 0, '2026-07-21 22:27:52', '2026-07-21 22:27:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_procedures`
--

CREATE TABLE `hospital_procedures` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_procedure_attributes`
--

CREATE TABLE `hospital_procedure_attributes` (
  `id` int(11) NOT NULL,
  `procedure_id` int(11) NOT NULL,
  `procedure_name` varchar(50) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `attribute_result_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`attribute_result_options`)),
  `input_type` varchar(10) NOT NULL,
  `is_required` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_procedure_results`
--

CREATE TABLE `hospital_procedure_results` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_detail_id` int(11) NOT NULL,
  `procedure_id` int(11) NOT NULL,
  `procedure_name` varchar(50) NOT NULL,
  `result` text NOT NULL,
  `registered_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_progress_picture`
--

CREATE TABLE `hospital_progress_picture` (
  `id` int(11) NOT NULL,
  `picture_location` varchar(500) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_schedule`
--

CREATE TABLE `hospital_schedule` (
  `id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `day` int(11) NOT NULL,
  `per_patient_time` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `doctor_name` varchar(200) NOT NULL,
  `schedule_start_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_services`
--

CREATE TABLE `hospital_services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `service_type` varchar(20) NOT NULL,
  `service_code` varchar(10) NOT NULL,
  `measurement` varchar(10) NOT NULL,
  `is_active` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `registered_by_name` varchar(50) NOT NULL,
  `gl_account` varchar(15) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_services`
--

INSERT INTO `hospital_services` (`id`, `service_name`, `tag`, `service_type`, `service_code`, `measurement`, `is_active`, `registered_by`, `registered_date`, `registered_by_name`, `gl_account`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Consultation', NULL, 'Consultation', '', 'each', 1, 1, '2026-07-21 20:03:19', 'admin', '1000-02', '2026-07-21 22:03:19', '2026-07-21 22:03:19', NULL),
(2, 'Minor Surgery', NULL, 'Procedure', '', 'each', 1, 1, '2026-07-21 20:27:20', 'admin', '1000-03', '2026-07-21 22:27:20', '2026-07-21 22:27:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_skip_date`
--

CREATE TABLE `hospital_skip_date` (
  `id` int(11) NOT NULL,
  `skip_date` date NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_template`
--

CREATE TABLE `hospital_template` (
  `id` int(11) NOT NULL,
  `template_type` varchar(50) NOT NULL,
  `template_name` varchar(200) NOT NULL,
  `template_content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_treatments`
--

CREATE TABLE `hospital_treatments` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `hospital_view_bill_and_payment`
-- (See below for the actual view)
--
CREATE TABLE `hospital_view_bill_and_payment` (
`bill_id` int(11)
,`service_type` varchar(20)
,`amount` double(19,2)
,`type` varchar(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `hospital_view_doctors_income`
-- (See below for the actual view)
--
CREATE TABLE `hospital_view_doctors_income` (
`bill_id` int(11)
,`doctor_name` varchar(50)
,`doctor_id` int(11)
,`payed_amount` float
,`payed_date` datetime
,`patient_name` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_visits`
--

CREATE TABLE `hospital_visits` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `visit_date` datetime NOT NULL DEFAULT current_timestamp(),
  `visit_status` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `patient_name` varchar(50) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(50) NOT NULL,
  `follow_up_id` int(11) NOT NULL DEFAULT 0,
  `extra` varchar(200) NOT NULL,
  `queue_no` int(11) NOT NULL DEFAULT 0,
  `visit_closed_date` datetime DEFAULT NULL,
  `mobile` varchar(13) NOT NULL,
  `is_free` int(11) NOT NULL DEFAULT 0,
  `waiting_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_visits`
--

INSERT INTO `hospital_visits` (`id`, `patient_id`, `doctor_id`, `visit_date`, `visit_status`, `registered_by`, `bill_id`, `appointment_id`, `patient_name`, `doctor_name`, `registered_by_name`, `service_id`, `service_name`, `follow_up_id`, `extra`, `queue_no`, `visit_closed_date`, `mobile`, `is_free`, `waiting_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 5, '2026-07-21 20:24:59', 3, 1, -1, 0, 'Mukera And Hulet', 'doctor', 'admin', 1, 'Consultation', 1, '{\"reference\":\"\",\"next_appointment_description\":\"\"}', 1, NULL, '0913144095', 4, -1, '2026-07-21 22:24:59', '2026-08-10 17:54:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_visit_encounter`
--

CREATE TABLE `hospital_visit_encounter` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `chief_complaints` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`chief_complaints`)),
  `physical_finding` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`physical_finding`)),
  `past_medical_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`past_medical_history`)),
  `hpi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hpi`)),
  `diagnosis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`diagnosis`)),
  `medication` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medication`)),
  `procedure` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`procedure`)),
  `registered_date` datetime NOT NULL,
  `post_procedure_diagnosis` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`post_procedure_diagnosis`)),
  `post_treatment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`post_treatment`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_visit_encounter`
--

INSERT INTO `hospital_visit_encounter` (`id`, `visit_id`, `patient_id`, `patient_name`, `doctor_id`, `doctor_name`, `chief_complaints`, `physical_finding`, `past_medical_history`, `hpi`, `diagnosis`, `medication`, `procedure`, `registered_date`, `post_procedure_diagnosis`, `post_treatment`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Mukera And Hulet', 1, 'admin', '{\"chief_complaints_0\":{\"title\":\"Dry skinf\",\"description\":\"\"}}', '{\"physical_finding_0\":{\"title\":\"j\",\"description\":\"\"}}', NULL, NULL, '{\"diagnosis_0\":{\"title\":\"Dandruff\",\"description\":\"\"}}', '{\"medication_0\":{\"title\":\"pa\",\"description\":\"\"}}', NULL, '2026-08-10 00:00:00', NULL, NULL, '2026-07-21 22:30:09', '2026-08-10 17:54:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_vital_sign`
--

CREATE TABLE `hospital_vital_sign` (
  `id` int(11) NOT NULL,
  `blood_pressure` varchar(8) NOT NULL,
  `temperature` decimal(10,1) NOT NULL,
  `weight` int(11) NOT NULL,
  `pulse_rate` int(11) NOT NULL,
  `respiration_rate` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `note` text NOT NULL,
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_vital_sign`
--

INSERT INTO `hospital_vital_sign` (`id`, `blood_pressure`, `temperature`, `weight`, `pulse_rate`, `respiration_rate`, `registered_by`, `registered_by_name`, `registered_date`, `note`, `visit_id`, `patient_id`, `patient_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '120/80', 35.0, 55, 0, 0, 1, 'admin', '2026-07-21 20:25:26', '', 1, 1, 'Mukera And Hulet', '2026-07-21 22:25:26', '2026-07-21 22:25:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_waiting_list`
--

CREATE TABLE `hospital_waiting_list` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `doctor_name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `priority_reason` varchar(50) DEFAULT NULL,
  `priority_level` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospital_waiting_list`
--

INSERT INTO `hospital_waiting_list` (`id`, `patient_id`, `doctor_id`, `full_name`, `registered_date`, `registered_by`, `registered_by_name`, `doctor_name`, `status`, `mobile`, `priority_reason`, `priority_level`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'Mukera And Hulet', '2026-08-10 15:49:03', 1, 'admin', 'Doctor 1', 0, '0913144095', NULL, 0, '2026-08-10 17:49:03', '2026-08-10 17:49:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hospital_xray`
--

CREATE TABLE `hospital_xray` (
  `id` int(11) NOT NULL,
  `xray_location` varchar(500) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_date` datetime NOT NULL DEFAULT current_timestamp(),
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_credit_payment_invoice`
--

CREATE TABLE `inventory_credit_payment_invoice` (
  `id` int(11) NOT NULL,
  `company_title` varchar(100) NOT NULL,
  `month` varchar(100) NOT NULL,
  `invoice_data_raw` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`invoice_data_raw`)),
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `status` varchar(10) DEFAULT NULL,
  `letter_number` varchar(20) DEFAULT NULL,
  `payment_number` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_daily_sales_export`
--

CREATE TABLE `inventory_daily_sales_export` (
  `id` int(11) NOT NULL,
  `department` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `reference` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_expiry_date_temp`
--

CREATE TABLE `inventory_expiry_date_temp` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `expiry_date` varchar(10) NOT NULL,
  `quantity` float NOT NULL,
  `new_ex_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_finance_maintain`
--

CREATE TABLE `inventory_finance_maintain` (
  `id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_grv_detail`
--

CREATE TABLE `inventory_grv_detail` (
  `id` int(11) NOT NULL,
  `grv_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  `quantity` int(11) NOT NULL,
  `manufactured_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `batch_number` varchar(15) NOT NULL,
  `tax` float NOT NULL,
  `item_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_detail`)),
  `average_cost` float DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_grv_main`
--

CREATE TABLE `inventory_grv_main` (
  `id` int(11) NOT NULL,
  `grv_code` varchar(15) NOT NULL,
  `recieved_date` datetime NOT NULL,
  `description` varchar(100) NOT NULL,
  `store_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `recieved_by` int(11) NOT NULL,
  `fs_no` varchar(20) NOT NULL,
  `machine_no` varchar(20) DEFAULT NULL,
  `receipt_date` varchar(10) NOT NULL,
  `receipt_date_gc` date DEFAULT NULL,
  `is_ethiopian_calander` tinyint(1) NOT NULL DEFAULT 0,
  `is_manual_receipt` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `scanned_documents` text NOT NULL,
  `cpv` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_inventory_count_setup`
--

CREATE TABLE `inventory_inventory_count_setup` (
  `id` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `is_year_end` tinyint(1) NOT NULL DEFAULT 0,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `year_end_committed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `id` int(11) NOT NULL,
  `item_generic_name` varchar(100) NOT NULL,
  `item_brand_name` varchar(100) NOT NULL,
  `item_code` varchar(30) NOT NULL,
  `preparation` varchar(50) NOT NULL,
  `strength` varchar(50) NOT NULL,
  `item_category` varchar(50) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `measurement` varchar(15) NOT NULL,
  `min_balance` int(11) NOT NULL,
  `compounding_measurement` varchar(10) NOT NULL,
  `compounding_volume` varchar(50) NOT NULL,
  `item_value` int(11) NOT NULL,
  `notify_expiry` int(11) NOT NULL,
  `is_active` int(11) NOT NULL,
  `is_taxable` int(11) NOT NULL,
  `is_perishable` int(11) NOT NULL,
  `item_registered_by` int(11) NOT NULL,
  `item_registered_by_name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item_category`
--

CREATE TABLE `inventory_item_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item_count_record`
--

CREATE TABLE `inventory_item_count_record` (
  `id` int(11) NOT NULL,
  `inventory_count_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `unit_cost` float DEFAULT NULL,
  `unit_price` float DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `remark` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item_location`
--

CREATE TABLE `inventory_item_location` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `location` varchar(10) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_by_name` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_peachtree_item`
--

CREATE TABLE `inventory_peachtree_item` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `peachtree_id` varchar(16) NOT NULL,
  `peachtree_description` varchar(30) NOT NULL,
  `exported` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_pricelist`
--

CREATE TABLE `inventory_pricelist` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_price` float NOT NULL,
  `store_id` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `registered_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_purchase_detail`
--

CREATE TABLE `inventory_purchase_detail` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_detail` text NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_purchase_main`
--

CREATE TABLE `inventory_purchase_main` (
  `id` int(11) NOT NULL,
  `purchase_code` varchar(15) NOT NULL,
  `description` varchar(100) NOT NULL,
  `store_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `is_urgent` int(11) NOT NULL,
  `is_ordered` int(11) DEFAULT NULL,
  `ordered_date` datetime DEFAULT NULL,
  `ordered_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_sales_detail`
--

CREATE TABLE `inventory_sales_detail` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_detail`)),
  `quantity` float NOT NULL,
  `unit_price` float NOT NULL,
  `vat` float NOT NULL,
  `measurement` varchar(20) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_sales_detail_old`
--

CREATE TABLE `inventory_sales_detail_old` (
  `id` int(11) NOT NULL,
  `sales_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `quantity` float NOT NULL,
  `unit_price` float NOT NULL,
  `vat` float NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_sales_main`
--

CREATE TABLE `inventory_sales_main` (
  `id` int(11) NOT NULL,
  `fs_number` varchar(20) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(50) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `sales_type` varchar(20) NOT NULL,
  `store_id` int(11) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `tin_number` varchar(13) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `credit_payment_request_id` int(11) NOT NULL DEFAULT 0,
  `visit_id` int(11) NOT NULL,
  `is_recieved` int(11) NOT NULL,
  `recieved_by` int(11) NOT NULL,
  `recieved_date` datetime DEFAULT NULL,
  `pos_bill_id` int(11) NOT NULL DEFAULT 0,
  `sent_to_pos` tinyint(1) NOT NULL DEFAULT 0,
  `compounding_status` int(11) NOT NULL DEFAULT 0,
  `compounding_amharic_direction` text NOT NULL,
  `compounding_done_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `inventory_sales_total1`
-- (See below for the actual view)
--
CREATE TABLE `inventory_sales_total1` (
`fs_number` varchar(20)
,`pos_bill_id` int(11)
,`total` double
,`updated_at` datetime
,`type` varchar(20)
,`payment_method` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_siv_detail`
--

CREATE TABLE `inventory_siv_detail` (
  `id` int(11) NOT NULL,
  `siv_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `batch_no` varchar(20) NOT NULL,
  `unit_price` float NOT NULL,
  `unit_cost` float NOT NULL,
  `average_cost` float NOT NULL DEFAULT 0,
  `grv_id` int(11) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `item_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`item_detail`)),
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_siv_main`
--

CREATE TABLE `inventory_siv_main` (
  `id` int(11) NOT NULL,
  `srv_id` int(11) NOT NULL,
  `siv_code` varchar(15) NOT NULL,
  `issued_by` int(11) NOT NULL DEFAULT -1,
  `issuing_store` int(11) NOT NULL,
  `requesting_store` int(11) NOT NULL,
  `description` varchar(500) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `recieved_by` int(11) NOT NULL DEFAULT -1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_srv_detail`
--

CREATE TABLE `inventory_srv_detail` (
  `id` int(11) NOT NULL,
  `srv_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_detail`)),
  `deleted_at` datetime DEFAULT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_srv_main`
--

CREATE TABLE `inventory_srv_main` (
  `id` int(11) NOT NULL,
  `srv_code` varchar(15) NOT NULL,
  `request_date` datetime NOT NULL,
  `requested_by` int(11) NOT NULL,
  `requesting_store` int(11) NOT NULL,
  `requested_store` int(11) NOT NULL,
  `is_issued` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_stores`
--

CREATE TABLE `inventory_stores` (
  `id` int(11) NOT NULL,
  `store_name` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  `registered_by` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_stores`
--

INSERT INTO `inventory_stores` (`id`, `store_name`, `location`, `created_at`, `updated_at`, `deleted_at`, `registered_by`) VALUES
(1, 'Main Store', 'Lebu', '2026-07-21 22:17:19', '2026-07-21 22:17:19', '0000-00-00 00:00:00', 1),
(2, 'Dispensary', 'Lebu', '2026-07-21 22:17:32', '2026-07-21 22:17:32', '0000-00-00 00:00:00', 1),
(3, 'Compounding', 'Lebu', '2026-07-21 22:17:42', '2026-07-21 22:17:42', '0000-00-00 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_supplier`
--

CREATE TABLE `inventory_supplier` (
  `id` int(11) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `tin_number` varchar(10) NOT NULL,
  `address` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`address`)),
  `peachtree_id` varchar(20) DEFAULT NULL,
  `payable_account` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `batch_number` varchar(15) NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `transaction_type` varchar(20) NOT NULL,
  `transaction_reference` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `unit_cost` float DEFAULT NULL,
  `updater` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `manual_sales_temp`
-- (See below for the actual view)
--
CREATE TABLE `manual_sales_temp` (
`id` int(11)
,`invoice_no` varchar(20)
,`pos_bill_json` longtext
,`total` float
,`sales_id_list` longtext
,`type` varchar(20)
,`response_data` text
,`fs_number` varchar(20)
,`machine_number` varchar(20)
,`payment_method` varchar(20)
,`transferred_amount` float
,`payment_reference` varchar(50)
,`sent_to_pos_date` datetime
,`response_date` date
,`created_at` datetime
,`updated_at` datetime
,`deleted_at` datetime
);

-- --------------------------------------------------------

--
-- Table structure for table `online_prescription`
--

CREATE TABLE `online_prescription` (
  `id` int(11) NOT NULL,
  `branch` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `image` mediumblob DEFAULT NULL,
  `status` int(11) NOT NULL,
  `patient_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `branch_from` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `branch_to` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `doctor_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `prescription_group_id` bigint(20) NOT NULL,
  `date_created` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_bill`
--

CREATE TABLE `pos_bill` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(20) DEFAULT NULL,
  `pos_bill_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`pos_bill_json`)),
  `total` float NOT NULL DEFAULT 0,
  `sales_id_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sales_id_list`)),
  `type` varchar(20) NOT NULL,
  `response_data` text DEFAULT NULL,
  `fs_number` varchar(20) DEFAULT NULL,
  `machine_number` varchar(20) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `transferred_amount` float DEFAULT NULL,
  `payment_reference` varchar(50) DEFAULT NULL,
  `sent_to_pos_date` datetime DEFAULT NULL,
  `response_date` date DEFAULT NULL,
  `is_void` tinyint(1) NOT NULL DEFAULT 0,
  `digital_payment_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`digital_payment_detail`)),
  `digital_payment_status` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_cashier_summary`
--

CREATE TABLE `pos_cashier_summary` (
  `id` int(11) NOT NULL,
  `summary_of` varchar(50) NOT NULL,
  `summary_date` date NOT NULL,
  `z_total` float NOT NULL,
  `deposited_total` float NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `cashier_name` varchar(50) NOT NULL,
  `cashier_document` text NOT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `summary_status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_machine`
--

CREATE TABLE `pos_machine` (
  `id` int(11) NOT NULL,
  `machine_name` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `extra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`extra`)),
  `receive_manual` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_maraki_void`
--

CREATE TABLE `pos_maraki_void` (
  `id` int(11) NOT NULL,
  `pos_machine_name` varchar(50) NOT NULL,
  `invoice_no` varchar(20) NOT NULL,
  `void_time` int(11) NOT NULL,
  `void_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sys_api`
--

CREATE TABLE `sys_api` (
  `id` int(11) NOT NULL,
  `api_group` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `api_version` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sys_header_information`
--

CREATE TABLE `sys_header_information` (
  `id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `mobile` varchar(100) NOT NULL,
  `tel` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(100) NOT NULL,
  `tin_no` varchar(13) NOT NULL,
  `location` varchar(200) NOT NULL,
  `logo` longblob NOT NULL,
  `logo_small` longblob NOT NULL,
  `active` int(11) NOT NULL,
  `logo_printouts` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_header_information`
--

INSERT INTO `sys_header_information` (`id`, `company_name`, `mobile`, `tel`, `email`, `website`, `tin_no`, `location`, `logo`, `logo_small`, `active`, `logo_printouts`) VALUES
(1, 'RANK SPECIALIZED DERMATOLOGY CLINIC', '0943515151', '0943515151', 'info@rankderma.com', 'www.rankderma.com', '00000121', 'Lebu Infront of Varnero', 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082, 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082, 0, 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082);
INSERT INTO `sys_header_information` (`id`, `company_name`, `mobile`, `tel`, `email`, `website`, `tin_no`, `location`, `logo`, `logo_small`, `active`, `logo_printouts`) VALUES
(2, 'GELEAD PHARMACY', '0115504312', '0114713505', 'rank.gelead@gmail.com', '', '0002289582', 'Lebu Infront of Varnero,Sabeh Spring Bldg.', 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082, 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082, 0, 0x89504e470d0a1a0a0000000d494844520000008d00000095080600000065db7953000000097048597300000b1300000b1301009a9c180000168549444154789ced9d799854d59987dfeaea6e9aad71055c50445444891a41dce3121de3828ecb24d13831891357a2a3c631711933d19899d1a83113976c9aa8a3a346c9b8c535ee0ee2868282b8302a880a82880dbd55fef85585aaea7babeff29d7b6f35fd3e4f3d745dee3de776f5afce3de73bdf922b140af4d34f181ad2be817eea8fc6f237473db12cadfbc822eb001b00eb0243d067d500148036e0036016d0e5d7403e07f3577431a2a581d3c60d62ef11cd3dce99b1a483d79675316371074f7ed4c18896065a9b7274a5f000b86d8f6181ce6becfd94358266606be04bc0aec04860536023a0c9e79a4e600e301f980efc0578acfc84ae028c1e9c67615b37e7bef419fb8e6c66ea568318da94e3f1451d4c5fdcc1fd0b57d1d609c39a738c1d9aa7502015c184614d17cd5ec0e1c07ec0b890d73602db145f07168fbd005c0b5c573aa9b300230736d0de0df72e68e7adcfba68c9e798b9b493e6861c235af23435407741af7a604d15cdb1c0c9c0cec6ed7e1189e6abc041c04ad0c891cfc11643f32c692fd05528b0d9e03ca0679d87589a8176e37b33634d13cdeec095e88feb927d80478bfdfd6dced35580218d394062a9627d606af1de360396036f02ef017f025e043e777ad7015993447331f0c304fbdb19f836f0ab00e71e045c0fac57757c72f1dfb391781e02fe17f8a3cd2d46634d5972ff8e640553e2787aff8c7706eea6a760aad918380eb803781db808d836e6fd45624d10cdc5e8c34e839dd02acc8f46e0d608ed6e059c0bbc5cbc7e8f086d44a6af8b663ce98c30e5d4327e9c046c12a3ed06e01f80c781db802fc4682b54a77d994b52eebf1b58e1f37f0dc0a9867d1d89469eff04861ab6db83be2c9a41ac9e48a6c57c6081cfffed0f6ce9a0cfb3805780bd1db40dd4cfea69149a1fec8884fe1c5a45d4b2650c47e6ff349983ff48f30d87fd6e0a3c8226cbe75b379e75d1ec0a7c17380a8d1ce5cc054e40e67b2fda90a806bbbab900fcbfcff12660df04fa3f0fcd730eb56c34ab8fa7bd8069c053c037e92918d0d0fe28da2ff2e2c3e22b4dfc8c71bba0fdad2498823ec781560d664d34e3815b9018a604bc661adea3490178d8e8bea2b2d0e778d273ad5d81a7317a5c6749343f0666a27d9b300cc37f957479ac3b8acf2c9fe307277a17627bb434f7dbb50f4c164433114d6ccf03f211db988a2ca6d5cc035e8ad8a6056f7b1c1b8a26f469b003b228c7226dd19c880433d1a0ad937c8e5f65d076143a804f3d8e1f40ba93f343807f8ad3409aa2b901b8dab0bde381168fe3bf455e7649f31eb0c4e3785aa34c39e700b9a817a7219a416836ff8fc6ed0e47239717b71bf7158437f15e3d59fbf044610c72bf8844d2a2d910b946eeeaa8fdeff91cbfc6517fb598ef71ac094d48b3404f87e58024299a8d8067917ba42b36038ef1383e8be497df8b3c8eed4ded0dcca4f810782beac54989a615596e4725d0d7053ec77f9d40dfe57889265117861afc8218eea44989e621606c427d6d89f766dd34606942f700f086c7b1dd12ecdf8fe7914d2c324988e676605202fd947391c7b136929bdb14d0de5839ebe2bfe591140b581d391119d7a2b91838c2711f5eec8ab7ede73a8f632e68033eae3ab62de99a383e427b5eb1f7e35cfe12fb92aed7dce91ec7de069e4ca0eff9289aa09c3497da4b8bfdfbedba87c2956886908e6da49c6380cd3d8e5f9a40dfcf20afbd72b64ea05f2f9623c1445e2d55e34a3437006b396a3b0cffe2716c1ab2d6bae4cdaaf78371e84957835568f23dc7b25117a29982425db3c03178db456e71dcef2755efc710cf813c0aed68e2fd8a75c3d6a2198af67ab2c220bc479b5f3aeef7b5aaf7ae2ce0b5d80bf83f170d5b8be64768699925a6d27323f36de0cf8efa5b45cfe5f6ee8efaf2635f34af7282a56886e3bff7932643f00e96bbd6517f8be9b9dcf69a90bbe240e454ee0c4bd15c4974272ad75c404f8fb53bf177c78cc37b549ae84791dc26e511c07dae3bb112cd40e030a3b65cb001def7e7c2d8573d9f198da153770dbe41428901ac44b323de0e5059e26c8f63d73be8a77acfc9c22bb1374e016e4aa01fa04a34915db9608bb83792001391ab6339ef5095f2cc806a2f41d796e01fe07e35584185681aa2aba635f69d24c3191ec7fec3b88ff295538ef069d9c2f023e0a70edbf7a442348b567647154eb5c93cabec45cf6ffebdc0fb46ed7f08cc287bbf03ee32395c055ce8a8ed9a548866597b81555d85288fa9d9563794005ebe2456f381c56887bb84abfda66b49d1bc51219aa347b7f0c1caee28939b47e869d0ca2a5f4666fd727e498d7cc021a88e7372e1dafa7bfc1de813a14234678e1fc4e643f22c6b0f9d9bb48082f4eb856a27adf92885595caaadb0d68fa66928b63d557aac9e863535d0192da1ed4c1408e615eb9335be4ecf00fc3f18b4fb4ed5fbd1066d9678988cd8c22a4473f33b2b99b1a48375064436dffc1979a86569d3d28f33abdedf8577d84918ca9dc9c761e717fd047aac66820a755c3db78df50634c4b1d7804cf3df4106bfff8ed79453cea032a36617c1d2b7fad18662c74b4c0006c468afc40b64483050259aae4281d6a69c5762e428bc001c8ddc026eb669d29406b4035e4e944c9b25dea372a4b2b0cfcc01f62463d9cb2b44336a70de453187679033d49ec82692254ea572349807dc13b1ad2554daabf68b7a53451620272abff46ba951211ac7f5c29e4099b9f7457952b2c03af4b47744f5219e57f5be7a591f86e5c810e91570973a95a2097e5d1e7d28a308ef0ef108fa06fd3dc94406f4c6e9547e0e8f112dcca3dc4b6e3c3022e2fd7423c17805db658228cba47f45bfd02c946e7d2e328eed12b29dbb5098ead7d0723d2d364413f71205e0bf22b4f36ed9cf13889e04731f341fcc2c6144d38272e15d8802ed5b900fee189450e869647caade49ee8d5b81edd01f2ead6fd7f9547e16bfa058762704e5a289ea443e05fb5d7773c288e65e346cd6620a2a33331d99bac32406fc2dfa869e8951505708465169d15e42b82c137350b6f01251b60f8e45b991334f50d19c4bb8b89d4928cbd52c9444717cc0eb56013f4306c2f3e8e96beb92eaad853059bae6a3f28425c2c6ae9f02dc18f29ad408229a097807d407611394aa6b16329c05cd9ab01cc5818f47ab995511fb0fc3582ad3d0de43cf15911fe58e57cd78278df4e31c1276a28a4b10d158f9d11e8f564b8fa0ca21410ccf1f01df4723cf35845ae045e2bcaaf7d707bceed5b29ff724b853da4f817f0f786e66e84d348763efaeb8379afcce46c51fd60f70cd3c34d9fe02f03fc6f753ce242a73fcde10f0ba724b70d02c19bf43ae9a75476fa2a9ded4b3641c2a333317b882606e04afa2e4d4bb01f73bbaaff24c5aef112c84b734091e4a3017917b51a9c2baa49668b6209970d2b580d3d0077f37c196ec4f035f299e6b6d5d9e841e31256eebe5fc0b591d60df4cef11a6cf904ec672336a89c63a656b100e424bf6e7807fa6f73fc0ddc8ba7c34954bdeb8946f25dc43a5397f25f0007abc1c8d9cbb4b2c46234db9cda69c27d1364a9d54e0f6265728db703aea8965e5ff379bf472aa945884fe387f20981ff2a9689e54ab6e645026237b13c8f8f83d94afee3ebcd3d7973304394c4d418fe13624be7f33b82f67dcb647b0c4a37ea2d906a5a888e95a63ca2d6825f7682fe70d4673b1d3d0866454be838d33591e1bff63e704158ddfe36977b22518d01ed52368b7fc78fceb34ae40dfe8ad517c79541bcf8b11afaba62e0413063fd16425dfad17bb2343e15c6457f14bd7fe21dac11e4ff0a5738933b0134d9fc34f343b257a17d1188962985e439e817bf99cf7164a353219658ae88d9348bf4e54a6f112cda6249fea2b0e035074c1a3e8d1751cdec908a62363e57ec5f3bc389b74ea28d4155e3e1ffb60e3109d06bb175f17a047d26fe89994f1a1e2eb70b4229a881e6597b1da8f662095919241d8118dd05b22534123725c6f45f3aad28ac36bb95d4073c85cf1e7ae62ff9fa2fc7d8b585d0ae873142a939a939697683648fc2eecd90c19ddce42a969afa36720db1f8baf3168c3b154666700c127afdba1bc3007e336d0bf9a15c804311f193a9fc561bab46abc449344d18ba428a54e3b0e99ee6f46f39af23a4cd5f9754bdffa5a8c45a3d9b116371981c1c8723d0938b278ec216496b815f8cc65e77e739abec881c867e575e4a9b791cf79a5c7841f27a0c9775a82f1e3cba8d2cc6c64a372869768a23a44d70ba3901d6726de09146b09e69ae22baaff6f128c421bc02fe028aba89768d22cca9924ebe09d520dbc85733b1a65ea851dd02af174eb86ab459323bb193a5db0c0e3989760ae239d6a32165c8e568666a45d62396deef2395e2e9c6f13b32471063803c32448d5a22950e7dbf621781def78abf289f07064ebe90b5c89513e63af91a6ba18445fe531fcbf20a5c882be661d36f1f7f6128d595da08ce39756a40d59703746a1c37d8972bb4e64bc4463e90197552e400e555e94461fbf9555bd133b5f9f97bd21e9e846d72c437b4b6dc897f71a82159ca8d7d5526fec896c397e2ea9bde2259a192873413dacac56a17d9825c84a3b0fed232d4162f900edcfbc4bb809fe562831405fa409f931ff2c6a035ea2995b7c25b901578b95481073d06eef272888eeade2b1a568afa5c3b0cfc9866ddd07fc1c581bed9ee7d01732873eff7cd9cf1ba29df21d0817071f962de35cec670ebf189b6c9741e842a27817c56e7f8cb6fe6723e3db729451dc222bfa0414653116c55c3ded73de6883be4acc247c8cd686c847e8127a961cb2c089686e441f6edc14602051cc476228f986bc8146894f5062c737b0cf2bb707727b18835c1846a3f0de92c57b6dfcbdfd2c4bedac15e19a05c88afb20129cb5bb4a90a8565f6a6dbc1d8c02c5a6d4380734a7f8a4f8fa108d18f38a3f2f43e278834a77044b3644739051481ce38a3f4fe8e5ba4948385e7629cb3f521c63e94c1414f892cdadfc8d0168048bf448af259a76e050e4647408fac30c440161b3d0b7e1759436acdc89c9259ba05df86d9075731334a2ac57e31a3f06a13015af1c7b7e6e135188fb587d197d792d33c2371063a113648bffc6e22b87c24e9348fb011a05c6a089e1662811c1e4e23d58710a7223e8ac3a5efd3e0e16db3297612b9a95c4980e84f10b29e04e306b035f4413d40928b3c408dc57de1d8d7c85ab335158eef45b8866361acdab53f347650931ee2b0d67a24dd0e36573565744194e7ace5f5e4ef496818216a2598e4281ad44d35b58714d5c8a268fa21c47a1d4205ba309e6782058fca77b66e39d15c2d2b099c5026ad5111aa1b0124d69a49888c431024d545d5555b360395a197a65f1b41c692c44d34cb4c9be1fb132884615cd64e47fba3eabe37deaa58e25c882bc2bf0a6cfff676da4d900fff0e3b0bc8b52a54426ac680e4529bf2ccdec49d38102025fad714ed6469aedb17b2ac4a9340384bb912b701c1a910005b4327bae97f3b2269aef1bb45122b668820ec32753ff820165a17a2ac0795912cd89044fa5db1b97d2b36e78688288665da2d50ac81a5fa1f78448252c4513a7ad53089704bb161f60e458164434fb5b74943287116ea7d952340b439edf80f6fd1e42351aac988251d04090394d9cba4559e03054e8230c96a2d91b59605bd11fadbbf8ea429f7f69f37038b2888fc73ed7e1d7e97d1e179820a271e1cf91145f23bc60c076c97d18e956b9fd2ec172210726886892daa0b4e658a2d7a4ac0757d7de58087c0b55303625c887f3a975a70970027554d5c4013720df2273c140b09166b18b8e1d3295f8416159cb6c1a94dfa31557e2f969aa798d6c6eba797106362b8e7a4d82301cb97a38cdce1a64a49989bcffb39eb7e607d865e5acd791e680e20bb483ff2794e8c86f8f2d1241469a02fed18859e142543bc98a7a154d39e35101b257d197696dab8683ae12eeb2ead001575259d4c282be209a122d28b1d1abc85e139ba0a2096a7e4f9a6b7190e989be259a121ba244953f8fdb5050d1ccc33fb02c2d6ec22098dd87be60a7f1632a9aeb4426cc8773459c8e8cb91385d6b8a22f8e34e51c428c72ce6144330d05bfa5cd036859e992be2e1ad0a6e825512e0ce384d58e2c8d66b9db22f038f0778efbe82d8f7058de42deffadc8fed3887c7e4b490072687f2f5f3c96479b98c38cefc38b7350d6f6509b99615d087f82aa94a4b189391d85bcb8a6f4c7b4e2522a7d624ac228a791d5518f8d28927504f230d816ed21b94a0afe1b420600849df02d2258f91b6b9e47564ecb74227e588f34d57516bad1ef51fe6a4331f1cb516cf902546fea0e644e18874288df37bcaf12130899a43aca2ae1c208d7c4e13594ddc13aab841fd6a2b108235e894a236e871bf3c7e9614e8e229ad75031d224781bf9c73add80ab225670bc0796fb768b5124c52cc33641a9e202c75545fd70cec5fda3e27d149b94748ada52a62a2b5cd4b03cd641bb816bb047fd7016a20d42572c42bf446ccff908d4c372fb4554b2d992c093e138dfa8cbf0cef81d978f9060d2ca329ae5c75339f719b7e7fcf154c232670a2831d22ea49b00db7a22ecaa6c807539c2c0ae2f71453317556db3a013e5b835f5fd8880b5685c8d34d68b83e1414fb418866fc060e71419eeb2e0b7532f238d65b62ef0ae30ec89d5b3fb34648e8ecafec44c7f6148bd88c632032984588d594ef88e4016ccb01c85529f6605ebd5932bd18c366e2ff066b4b5dfc891848b04f8162af39725ea654eb393717b8b829e58219a469b8fea049465a2b70feb24e07a931e6d69c2b68ea78b916600f6053f024fac2b44b3b0addb4a3857a3443c0f7bfcdfc7c8a26952806b456781ae82e9d0b00c19cfac70219a0bb02ff81178095fe11a510016aeec66644b035df17fd557d08a6867948baf05ede2de89b299c7229f83651d05d66eced198cbb1a2b340838d725621f7d6bd4c5ab37f3c1d08fcd0b84d0896b707a812cd353b0de5e4e9cb79656927ad4de1a73b431a73ac37204767a5e09e2dbe4c59bcaac0c4751bf9e69881fcf895cf682f1468c9998d37962b134bd11c8f41262b0f16a1ecf381a810cdd6c31af9d5cead3cfb7107c39ac3fd010a05b87f413bcf2de960cc903c8582db0aaacb3a0b8c1fd6c8b8d63c4b3b0ae46dd73ccb0ddb5a61d0c6fe680e7898415b5e3c807796534f7a78ee8d1d9a67ecd06851a9fb8e6ce6b419cb99beb883ad5a1bc9e524261734e6349ff9b4a34063ce5ca0ef18b655329a3553598db8340295ea3d95fe6d40fb40bb21e7ab03d0e3dd25bf0e73b269f2e9c18d39ae9bdcca792f7fc67d0bdad96470032df91cddf557b4d9d2327d39725c1bc46ac194921b81566bd505c38611c2421b93e9c8f73a30e619cb1b72f093ed87b04ef3e7dcf84e1beb0fc8b3ce80ba13ced3c853d0c2eb6ee3e22bab849e543b0b0a3b6bfc202ede4e15f5e67edac5e75d05abe57c127c4ef682035df020de66919a388d243c68a301dcb45b2b276e3190ce6ef86895991d28092c932466912ee0ab512eb4148da7f97d784b03276c3190ab260e65ede6069675d4cd73ea0ee410d657398a88aeb496a2295f19f460cbd63c3baddbc4e2f6ba110d28ab545fe42c628422251ae8beb2cbdc9ee29adb503edfbec4f9c8553732898aa6aec698d51c89f200f705a60217c56da42fa7d4b062194a205dcfbc037c09a3c97dbf6882311385aeb6a57d2311b80e85de8632e0d5a25f34c1790a7df8d6d18daef80b1a5d4ec0d809bd5f34e178130927525e978498061c841ea966a34b396954cbad770ac8f47e0bf2503c061892f2fd3c881cf3efc64d006305752d9a7c0eba0bdaed1eda94f85a7e26caf977012a8b33196575188576a9ad3fdb4fd084763eb01445a03e8ff205cf33eeab26752d9ab6ce0203f3b056738ef73fef66f4e07cb50358127c885c0b4aee052dacae35deceea30df5c8d7fcb155f3ad6597c7520ff9e8f505284d4b3c7e70aae1c5efae9b3f44f84fb094dbf68fa094dbf68fa09cd5f016654861d619aeb900000000049454e44ae426082);

-- --------------------------------------------------------

--
-- Table structure for table `sys_measurement`
--

CREATE TABLE `sys_measurement` (
  `id` int(11) NOT NULL,
  `measurement_name` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sys_notification`
--

CREATE TABLE `sys_notification` (
  `id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `link_type` varchar(50) NOT NULL,
  `notification_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_notification`
--

INSERT INTO `sys_notification` (`id`, `notification_type`, `status`, `description`, `user_id`, `link_id`, `link_type`, `notification_date`) VALUES
(1, 'Doctors Waiting', 1, 'Mukera And Hulet', 5, 1, 'visit_id', '2026-07-21 20:24:59'),
(2, 'Nurse Waiting', 1, 'Mukera And Hulet', 0, 1, 'visit_id', '2026-07-21 20:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `sys_user`
--

CREATE TABLE `sys_user` (
  `id` int(11) NOT NULL,
  `username` varchar(32) NOT NULL,
  `name` varchar(100) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `role` varchar(20) NOT NULL,
  `extra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`extra`)),
  `email` varchar(30) NOT NULL,
  `mobile` varchar(13) NOT NULL,
  `registered_by_name` varchar(50) NOT NULL,
  `registered_date` datetime DEFAULT current_timestamp(),
  `registered_by` int(11) NOT NULL,
  `digital_signature` longblob DEFAULT NULL,
  `pin_code` varchar(100) NOT NULL,
  `login_id` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `sys_user`
--

INSERT INTO `sys_user` (`id`, `username`, `name`, `title`, `password`, `active`, `role`, `extra`, `email`, `mobile`, `registered_by_name`, `registered_date`, `registered_by`, `digital_signature`, `pin_code`, `login_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin', NULL, 'd89921949615574a42c781c919fdd913', 1, 'SUPER_ADMIN', '{}', '', '0', '', '2026-07-21 18:38:23', 0, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', 'mo6pn8o75jgle3n5v4rks8r5qc2d8l59', '2026-07-21 18:37:09', '2026-11-03 13:20:41', NULL),
(2, 'Doctor_1', 'Doctor 1', NULL, 'd89921949615574a42c781c919fdd913', 1, 'DOCTOR', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:08:02', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:08:02', '2026-07-21 22:08:02', NULL),
(3, 'doctor_2', 'Doctor 2', NULL, 'd89921949615574a42c781c919fdd913', 1, 'DOCTOR', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:08:30', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:08:30', '2026-07-21 22:08:30', NULL),
(4, 'Nurse_1', 'Nurse 1', NULL, 'd89921949615574a42c781c919fdd913', 1, 'NURSE', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:09:07', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:09:07', '2026-07-21 22:09:07', NULL);
INSERT INTO `sys_user` (`id`, `username`, `name`, `title`, `password`, `active`, `role`, `extra`, `email`, `mobile`, `registered_by_name`, `registered_date`, `registered_by`, `digital_signature`, `pin_code`, `login_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 'doctor', 'doctor', NULL, 'd89921949615574a42c781c919fdd913', 1, 'DOCTOR', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:09:56', 1, 0x89504e470d0a1a0a0000000d49484452000002b7000001670806000000bc4b61c500001000494441547801ec9d079c1445f6c76736677206c920a0185004232806f0c4082a26d013fea2e0994e0c77e29d1103623ac19c154c18404c202a22820a084a1050909c37e7fff757ec8cbbb03bb30bb3cb86b79f7e5dd555afaa5efdaa66fbd7afabbbc33cf6670818028680216008180286802160085413048cdc569381b46e18028640792060751a0286802160085435048cdc56b511337b0d0143c01030040c0143c010a80c0854521b8cdc56d28131b30c0143c01030040c0143c0103004ca8e8091dbb26366250c014320f408588d868021600818028640481030721b1218ad1243c01030040c0143c0103004ca0b01abb72c0818b92d0b5aa66b0818028680216008180286802150a91130725ba987c78c3304428f80d56808180286802160085467048cdc56e7d1b5be1902868021600818028640591030dd6a808091db6a3088d60543c01030040c0143c01030040c815d0818b9dd8583ed0d81d02360351a02868021600818028640852360e4b6c221b7060d0143c01030040c0143c0103004ca0b0123b7e585acd56b0818028680216008180286802150e10818b9ad70c8adc1d02360351a0286802160081802868021b00b0123b7bb70b0bd2160081802868021503d11b05e1902350c0123b7356cc0adbb8680216008180286802160085467048cdc56e7d10d7ddfac4643c01030040c0143c01030042a3502466e2bf5f0987186802160081802550701b3d41030042a0302466e2bc328980d86802160081802868021600818022141c0c86d48600c7d2556a32160081802868021600818028640d91130725b76ccac84216008180286c0fe45c05a37040c0143a044048cdc96088d6518028680216008180286802160085435048cdc56b511337b0d0143c01030040c0143c01030044a44c0c86d89d0588621600818028680216008180286405543c0c86d551b31b3d71030040c0143c01030040c0143a044042a90dc96688365180286802160081802868021600818022141c0c86d4860b44a0c0143c010d84704acb821600818028640481030721b1218ad1243c01030040c0143c01030040c81f242a02cf51ab92d0b5aa66b0818028680216008180286802150a91130725ba987c78c33040c81d02360351a0286802160085467048cdc56e7d1b5be190286802160081802868021501604aa81ae91db6a3088d60543c01030040c0143c01030040c815d0818b9dd8583ed0d014320f408588d868021600818028640852360e4b6c221b7060d0143c01030040c0143c0103004ca0b0123b7e585acd56b0818028680216008180286802150e10818b9ad70c8ad414320f408588d86802160081802868021b00b0123b7bb70b0bd21600818028680216008544f04ac57350c0123b7356cc0adbb8680216008180286802160085467048cdc56e7d1b5be851e01abd11030040c0143c01030042a3502466e2bf5f09871868021600818028640d541c02c35042a0302466e2bc328980d86802160081802868021600818022141c0c86d4860b44a428f80d568081802868021600818028640d91130725b76ccac842160081802868021b07f11b0d60d0143a044048cdc96088d6518028680216008180286802160085435048cdc56b5110bbdbd56a3216008180286802160081802d5060123b7d56628ad2386802160081802a147c06a34040c81aa868091dbaa366266af2160081802868021600818028640890818b92d119ad067588d8680216008180286802160081802e58b8091dbf2c5d76a37040c0143c010281d02a6650818028640481030721b1218ad1243c01030040c0143c01030040c81ca8040f524b7950159b3c11030040c0143c01030040c0143a0c21130725be1905b838680216008ec5f04ac7543c0103004aa3302466eabf3e85adf0c0143c01030040c0143c010a86108ec23b9ad616859770d0143c01030040c0143c01030042a3502466e2bf5f0987186c07e47c0dbab57af889e3d7bc6f6eddb37e9d8638fad73d24927d53be38c33ea73dc40f13e7dfad4e2384e7afbdddaca6680d96308180286802150e10818b9ad70c8ad4143a0522210d6aa55ab986eddbad56adbb66dc3d8d8d8e61d3b766c5da74e9d2e3366cce83967ce9c53befdf6dbb37ef8e1870bbefefaeb8bbff8e28b4b67ce9c79e977df7d77e1975f7e79f6b469d34e25dead53a74e2d0f3ae8a046aaa75dbb76d195b2a7669421600818028640a540a0bc8c30725b5ec85abd8640254700021ad9a0418384c68d1b37484848e8b461c386be8b162d1af1e79f7f3e909393f3daf2e5cb3fdbb66ddb0cbaf1516e6eeeabdbb76fff5f5a5ada43999999f7a6a6a6de85fc1719939d9dfd785656d64be9e9e91ffff2cb2f53a8e3c9f9f3e7dfb07af5ea53636262da40906b514724629b216008180286802150ee0818b92d7788ad0143a07220307af4e888468d1ac5272626d613e95cb162c5c0e4e4e4a7376fdefc754a4aca57e9e9e9af646464dc0e79bd08c27a2c84b60d96d743129178240e892d469497407a6da413d23f2f2fef9f393939af53c77408f20b616161574445457525afcef4e9d3230843bc59758680216008180286c02e048cdceec2c1f68640b543203f3fdf3b65ca9468bcb20debd7afdf61ecd8b127d2c9117859ff171e1efe369ed80721a067a3d7c1ebf5d6f17abd71889612844346897a510fbc517677052f691190db68886d1c24b905759d0699be13c567917f9f7cf2c9bd5bb76edd68eedcb9e6cd0510db0c0143c0102877046a5803466e6bd8805b77ab37027867c3264e9c187bd8618735c54b7bf015575cd10f727b0324731c5ed947f0d05e171d1d7d06f14320a18d4542093d3059078ce212d23d0a5de23eec20b65eea8a494b4b6b08a93e94b62f82f4deb572e5ca1b7bf4e871cc51471dd5087bc3f7a1092b6a08180286802160081441c0c86d1138ecc010a89a088c1f3f3e72f0e0c18d9f78e289aed75f7f7ddf6ddbb65d0381bd9bf0bfc8103cb427422e3ba5a6a636dcb973670c84d3b965093d3e2925992d1340aadb5700621b919999d980760e23ed4208f56d3ffcf0c3858f3ffe78e7071e78209e74671379b61902868021600818027b8d8091dbbd86ce0a1a02fb170191413cb57190da5677dc71c7d11f7ef8e12008ec4d9b376fbe75cb962d9713ef0399ec823480d446a1ef0c865416f1caea180fabc727858f5d8132ec54d627be623a561c7b5c1bc423c3c3c39be24d3e16b9f2fbefbf1f76d34d37f5ecdfbf7f3d6cb4ff4900649b216008545504cceeca80809d482ac328980d8640191080d08641686b3768d0a0fdc30f3fdc1b527b15ded8db929393af82409e01413c8c78233cb731c43d91917f2d6dc57bea27b61111bb9eeb928e3cac3e297c5c06b39caacafac425b0f3915ba5ab0dd9909b9beb8de18fec8e7895cf8b8a8a1a3a65ca94134f3bedb446e8d9ff2580b1cd1030040c014360ef10b093c8dee166a50c81724760f706b436f5f2cb2f4f7ce28927da4c9a34a92f1ed99b535252ee82c80e4e4f4f3f81e3b64822e27ed7e1e1bb96b2e21d75045724933c8fc8a5ea86542a2822d2f149918c7d38f0d921922d910d6a03f2adb5bee1d8a7f5b8a74278affcf2cb2ffbf6e9d3474b176c89c23e606e450d0143c010a8c908b893604d06c0fa6e08540504446c6fbef9e6e6efbdf7de80cd9b378f8198de07a91d8827b42b645164308a7ee8e12d825d1b647157843d04d2efb1459f94a29bc8a652f09a3a3d853af6a5fb082a1e568f2fae7c2d6550e8139f37b8b08eda56be4289e2befa15a22b229b485dc762dbd09933679ed5a3470fbd7e4caa268680216008148780a51902252260e4b644682cc310a83c085c7bedb5c7406a1fdaba75eb7ff1bc9e0e096c811754ef96d56f58e4b05c8c2d209f1e1165c8a7272b2bcb1f8794ba87d14480159701906e477ea5afe3d208045d6a5eead0320a3d6c76d5dcb973cf27517d23b0cd1030040c0143c010283d0276f2283d56a6595d11a884fd92a7b67efdfa89b56ad5ea56b76eddd190da71786aff06c16cc2edfc2808a5575e5042dddadf671189150c85ebd33184d31158daf5c82bebcbd7b1082c44db797a0b08aa8af845bafe830091ddf4a2e85717eafb0745fe3660c0805d6b2b38b0cd1030040c0143c010280d02466e4b8392e91802158480482d84b6ce55575dd52d3d3dfd1a3ca50f120ec7537b704c4c4c3444d07969e3e2f4b1b0d018e523b6beda68c31775a1f24572b1c59168115bc8a7f3d02a4d4a2a23898d8d759e5dc5554e79c144bad281d4bab5c1c42368af0364fac69f7efaa925c7b6190286c07e44c09a3604aa1a02466eabda8899bdd51201bd01a15dbb7649575f7d755748e16590c67fd1d16178698f86d83680e8851377c451eb5e5353537d4410b5d06eb4ef2a14e994775664d69706e9743688dc2a4fe9d8e6bcbb8a43c45d59a5a99c3b08b2533b8555542e272727023964d9b2657d7bf5ea155338dfe286802160081802864020048cdc0642c7f2428c8055571881825bee7178640f18376edc31ab57af1e9c969676331eccff4b4e4eee038195d7324a4412a2e711a98d8c8c74e452f5a0a7609f448454e45284525250593e6979480e6999d89746fb29e4ed24dc4eb815f2ba953c17629fd292b12d9d32b9e47b641be98a0614f43dd4e9963c48b170b9e8e86861f3b76fbef9e6909e3d7bc62adfc41030040c0143c01008868091db600859be21106204e4a5eddfbf7fe29429533a431c2f8024de0691bd0f6fed3ff17c9e8587b6231ed218c4ad6715018438ba87b944fe742c93207f0af659a82f1f1bb20853902d54b81a5948fb5f22ef42b85f8c8d8d7d1c427b1f64f8bfe4fd07e2ea0462aaf09eb8b8b8b1d83691f23f61d73a7452a9d3115de2256ee8b83cea71cb1c742c51221e6b79ab8fc243fc7fdf7efbed2123468c8856ba8921506d11b08e19028640481030721b1218ad1243a074080c1d3a3472cc98318dbff8e28b532083ff80c08d82cc5e02b1ed09316c06b18b8640baca20752e84543a4fa842f2fd9e5bcabafc7dd8e5d06626f23b3293ba5fa3ae31b43f8af83f69ef366c1c0db1bdfbe69b6fbef789279e7890bcb1e88c83dc8e23fea8e213274e7cf8ce3bef1cd3a8512311dd5bb1eb1ec8f8fbe8882467a353e2a63e52bfcb579c322eae1d36687d716dc253b1e58c59b36635a74da529dbc41030040c0143c01028160123b7c5c252e513ad03950f01af88edebafbfde1d82f60f3cb4a330f15ce26d2174bae5eede512b0f26e97e02ab38045381bb7d8faef3e6ba04769477c70a21a8cefba97861811ca2b96b2b88e791af65063309ef80545e4bee2d9d3a75baefa5975e7a0ab23a113b3ec1c6ef485f42f827dee69dc3860dcba68d7cd2dce68b0f1c3830f7a69b6e4addb061c30aeafb1412fc0236df83d29dc83488e90ec23ca4c83a61ca2bc943fd2ef4edd447c515e239f6624b3deaedb578f1e24320d1c24ad926868021600818028640b10818b92d16164bacea08683d6bb76edd0ee016795f6e990fc5933802a27445f7eedd4fecd7af5f63c85a85cefdf8f8f8862fbef8e23088dced90b52110cac3f0d6d602e77db2430451423deea12eea76045269904a25bb74c8611ec4762bedce266f0298dc48daed2888cc7e842df3162c58b0f2e28b2fde29b24afa5e6dd49d7ff5d557a750f722307e874af4c1893708d7817f2ea497a8c7816fc4cf0000100049444154bd75011d4fb03fecd55a637d3fb81d36f6e6e2a045b032966f080447c0340c0143a03a23b04f27d6ea0c8cf5adea22d0b265cb26efbdf7de453ffdf4d37fb945fe2f08d58d90cb1bb8fd7dd3bc79f3467ff6d967f7fdf7bfff1dd4bf7fff46e5dd4b91ec98989813207db743eeaec51b7922520fe21992f7b78a204aa8dfff509608a1d220baf9b4934d7c595454d42bc4efc096db69ffc1175e78e1553cb473e8ff0e9159cafb3db2a4edf3a6fa20b73b478c18a1369e8258bf851d5a8beb3cb8c44bd50663a6b5c65efa541b3972e5ca95077ff0c107a17b0f5aa9ac302543c01030040c81aa848091db7d1c2d2b5e79108030791b356ad486dbe3574168af87600dc063db1d32d776fbf6ed07a4a4a4b4277e341ec081b56bd7befef3cf3fbffcacb3ce6a551e3d80388abcb69e3469d2f0a4a4a4db69fbd2b4b4b436b41f253b216a216b16e2e8bcb522b3542a0f6d0e1eebedf45feb681fac55abd62da4df7fd14517bd88e7783aedaf80d0a620411ff8a2dc3e6d8f3df6582624f76788fdcbf47d32e1462ad4036cce7b4b3ce086bd6ea945444484bcb7ad2222228ec7bbdc246021cb34040c0143c010a8d10818b9add1c35f7d3adfad5bb748bc935db76cd9321c427b293d3b04b2170ba10a8748c2ffc2bca48741f8c2398845af2b79974d9f3efd42bcab8dd10fd926620b71ec989898380c9baeddb871636f485d120d8461836f998023a4a4edd5463f3c12ea557df990f95cfa954adaaf783bdfc063fd9f0e1d3afce7a8a38e1abb63c78eb721f48b9f7beeb964f2f58aaffcbd6a742f0b416e736ebbedb685e0f13a63f129dee374b0f740c04b552365b4ded88b7e1d2e108e4c4d4d6d3777ee5c91dd529537a5fd8a80356e0818028640852360e4b6c221b706438d805ef23f6fdebcc32177ff0769ba303939b92504ca913f5f5b3e5249bed6702a59af996a03f13bf39d77de399a847d7e0a1f8fa8f781071e88bffcf2cb3b416acfc78e0b486b4ddde1781c093ceead078a60ab3faee3b20804d5a79e0ff1cba16f3b69e757da7c1b0238ae458b160f43f6c72f59b2e48bafbefa6a934f797f8610dcac4b2eb9647ebd7af5decdc8c8f809529e4b18d424f595fe393d087b14b81dc0c111b367cfd6c50251db0c0143c01030040c81a208541d725bd46e3b3204f496002f5ed7841933661c09011a8677f23cc86b130890d6693a84207f1e115d88917b038148a6489548137a1184ed49eb71e38d37eed33a4ec89bbcc275088fc3bb38145b2ea4dd6678546926cca3f621741e0e9c5ddae9586159047b7dea5a52b093fa1ca9a5ff8fb46ddb76ecc9279ffccae2c58b7f84eca7f9142b4bf8e4934fa63469d2641604fc6d6cda8a04dd20ed452e5220f07a08efe86baeb9a641d0c2a66008180286802150231130725b2387bd7a74ba5fbf7e8993264d3a1a82371c127916c4af1ec4c9bd528b34478a1442345d872146babdede2105a915d796be3219e072c58b0a0aecbd88b1d84360c69141717770e76fc03427611d5b48344d34c84960db875a3a4b9b8428988b7c2328a1ec84aa7cc52fafb7aab56adee1d3264c803dcf67f69d1a245f33ff8e0834a476ab1d5bf2d5cb87023a07c4adf6741c8d5177f5e491130751705f4d703a631393939edb93068ad57ab9554a626a65b9f0d0143c0103004762160e476170eb6af6208e0b1adf5c9279fe82d04574126fb627e6dbcb65e882a518f23b11ccbbbeb27943e922b920441727a94d56f2081ba125c42197790daa871e3c675a6d88db4771da4ad37e44d449966bcce0ef244a49d282e4157815f44c229e0489c2f51698a539f024936a44ea4f615ecfe6f83060dc6ac58b1e2b5c71f7ffc57ecc892426517fa987fd34d37adc4abfd0e58e96b686efd2fe97ed315f7f55d89f4d58da1428ec3c9af87f49c306182bcb824d9660818028680216008fc85804eec7f1db998ed0c81ca8d40dfbe7d93f0d89e0cc1198aa5bd11adbf94179668d1ad80101549549ac4978847373c2121416f37f025050d29ef6ddebc79dd871e7aa8dfb66ddb6e4f4c4c942d0742d8a290a0e57d0af4c1ad01a63e47c495ae348522c078a24590f321b5ba8dff2ea4fc3e3c9ef78f1f3ffead0d1b36ac945e551388780a7dfb01f986be648bc8aaffbe7e284e9eefb0b8301e9d63c0c6c86d71e8589a2160081802351c0123b7357c0254c1ee7ba74e9daa87b4ae14c1c1fe44a458624b7ad00dd2988f47372b2525a5d49e4f886524c4b63de4f2d2f4f4f41bf1ac9ec1edf204c4fd9ea8d3b58b7d2e0cb4930eed3b621b1b1bebf750faeac0139d477c3d847922047adcfdf7dfffce2db7dcb2525f0b0b546f65cfc373bb362e2e6e6a767676b23010a1f789cf76faed8bee1e46524617120d09f77aec77afb4d4c7a66808180286802150a9117027e34a6da1196708fc8580884c63c8e499480f089f3c77fb3487f110e641b4f47958c95f2d9510ebd6ad5bdc3df7dc7304c4f6ef90b3a110b0232058b1903457024fa4ff2d08226b2e31c88ef69d0644d685d42992abf7d56ea3be2fa9e771c20937dc70c30f37df7c73329ecf52ad55759555d2dd33cf3cb3232d2ded27cc5b86e736873e3a82af9034b731362edc7d07de1af35a841d8f38e208fb1cefee00d9b12160081802fb1181cad0b44e1295c10eb3c110280d02f5214217437a0620f2d8967afe16264d851a821fe5efc4e3ba62c48811db0ba5171b4d4a4aaabb74e9d25320b6ff87c2a01d3b76b487906a198248b77b68cc477221df8eaca11770a33f7a48aab08ed6a0664070e7d3c797a8efc16bafbd7602dedd0590da8cc28a55393e70e0c05c3cd56ba3a3a3bf078334faeabaa350d89116103f064ededbaebffefaab96a4b8b2b633040c0143c01030048440a9c981944d0c81fd8100de52bdb05f1edbbf43faae82007546bc3e8fe7ded804d9d557b2f4d68179b56ad5fa4a5fd22aa99ee9d3a74790d71aa2390892795d4e4eceb94833ea88c006476cc92f42c644ce94164ce88753c10b9c4f3c9b83e5f4f14d3cb563ead7af7f3fc7531e7ae8a1cdb4a5577f71587db62953a66ce6e26036b8eea07f22f5ae7360eb428fa7e400721b861c0456bac82959d1720c0143c01030046a1c02466e6bdc9057ad0e0f1830206adebc792df1e6e9f55a237273735b4368dcbcc5e3bad79d814c654140e7432adfbaeebaebbe2ba9220858f4a9a79eda09127b05246c3864ac07a42a5efad8e1f7ba5257912f8e4182ddabc8a4174ce85b2eb7e8b753df97c42774e9d265cccd37dffcd6e6cd9bd7052b5b95f37bf7ee9d81f7560fc5ad653c72c0b82cdd11b9ed989e9ebe576fb9284b43a66b0818028640a544c08c2a110147124accb50c43603f22a0f7984e9a34a915c4f10248ed7598d218efa6df430a192469afb65cc8d442e4596ef94fe6767f4a71b5346ad428be7ffffe47e149bc06222d8ff181e845416edd1204d25de8b303e24bb6c74f72a5e71202ec284b35395a77fac6f1c71f7fdfd9679ffdd48f3ffeb8189b720214ab36591d3b76dc0cc15dcab8a60184eb17e352e495682e71cf5d18494dc1af1ea13ceb04b61902868021600818021e8f4e1086832150e91090c776c284091d20b61762dc50084f1308ae97b81eb652e0de1b4bbe23bbe4bb34edf07e1621473e1d6ef5ebd67706c73f51d7d390d777efbdf75ebd6b55c5fc0229f5366bd6acdef6eddb4f44ef468e87507f5d427ffba43b7d8512c8993bd64e240d7d459d284e9b2e0e1953283b7249db42d99984e3faf5eb77f717fc41e68b25da2a548eb2dfaabee79e7bb6e37d5dc69839722b7cc059af3f2b8d4de1e0d7a273e7ce31a551361d43c01030040c819a818091db9a31ce55a697109b30bc970ddf7ffffdde90beab307c08d254e9847b6c901b9786ae0782e448adc8a58f6c8a2c291e1f1f9f999d9dfd1bca6f737cf775d75df7d6ead5abb771ecdff0968675ebd6ad3e24f8e84d9b365d493da3c8d40722b4e69768f15be1b6151799f569ca266cf711f23cec4d256f05767d821d8fd1d6ada79f7efa4b1f7ef8e19f9413e925bbe66c29fc252525fd0ed669f41aa87641003e6e2c490bb851a00de4d82d1309a868998680216008ec350256b0aa2160e4b6aa8d5835b657cb102eb8e082ceefbefbeee0acacac51109c0b202f2d10f78105427fef2182fe35add1d1d1ce8b0b417224520453a412e2a863bd366b476a6aea74d21fb8f6da6bff3b7efcf86963c78edd4a1dbb9814b5f6ecd9331672db6de1c28522b577d0fe48da3b1289409c775821655cbbbe90a26e539e220a258a4b6493426c911dcbe2e2e226d1af7b8f3cf2c8db9f7cf2c9476fbbedb6ef3ff8e003113ba9d538c1439f81d7fa4f70d16bcf7221fc0e0361e88bbb841276e8b5014f5b775b023e966c08180286404d44c0c86d4d1cf54ad867484ed40b2fbcd0e9cd37dfbc14f386404e7be269d5676c8b9da3901a4738d1f5f808a4e2229d22457848f5be59114a7dd9eb73f2fef7cf7ffe73d2238f3cb274d8b06145c8e4c48913a37efef9e7c3d0194a5d43a3a2a28e870837a6ae801e5bf49d7751b6a83db5abb884b22e8f7ab406378dbecca54fcfa7a5a53ddab163c737bffdf6db9f860f1fbe0d422d1b55558d1470caefdebd7b3217285bc12ab73008e4153e2c29de980c5b960008b619028680216008ec42a058e2b02bcbf68640c520d0b76fdfe84f3ffdb413ded241909cf3626363db4214a3213bf01bb7ccb54443d0715e5b29404afd8497f45c3c7a1b219a9f422a9f86487e71df7df76da342bfb75665c80fbbe38e3b5a423a2f888f8fef4fbb2db0239a74af08ab740209ed384fae4f47c79282637922d753e7271c3f52bb76ed97e7ce9dfbf3e2c58b53b0a346935af0f06f8989891960be1dccf52a348727f8f8c7d2afb867c44b993ae806bd08d9b3a8a5180286402544c04c320442828091db90c06895ec2d0222b69f7ffe79878c8c8c81909441909503d2d3d323452c0b91c412ab87c0ba3ce942905cbc6009c026ea9846c2d3175f7cf18c279f7c728f07b568cbdb90bfdf7ffffd1474fba5a6a6d68f8989714b2020d9cef34af9801bc4d591306c77fa0a5580746dbf53cf3bd47d1769ef6fdab469dd11471ce1081cc7b61520c0c5875ecb96cc186617c2af203770c0182650c6c86d60982cd71030040c811a858091db1a35dc95abb303060c88fdecb3cf0e84500e84005e8ce7b43904351c92e349484870cb0d202fce685fe80e0aedb8ddef8ee4b575118fb866fea6dcdcdcf7f1003f491b335f78e1853dbeec457ddeb8b8b8e63b77ee1c80d7f62a08522bda0fcbc9d9f5062ec8b623ab05759618c060d5a05fe887d6f9ea0311ab216d1390ffd2d60f54904a1b45bcc6a4d90602609e0946c98c7d0ea1c312ac9c0797ec801bfa898c81bd0a2c204a96690818028640cd42c0c86dcd1aef4ad3db5ebd7ac5bcf7de7b07424acf87485e0cc13900cfa95b830061f1a4a4a43872a3b86477c37de4474458790524371f4fee36ea7a0372fc281ee03993264d2ab28e53bad41756bb76edd69999994391517857bb783c9e70da77845a75aa7eea2139f8265d09f58abc8a1dafa35f6f70bbfd75fab1813ca507afa8e66a64e36d4f0577bd1ecda100968ee4ba83c03bf3dc06c6c7720d816211b04443a03a2360e4b63a8f6e25eddb881123a2bff9e69b8e90be0b20a08ed812f75b2b62a3035fa87861810479208f7e7a9364e60000100049444154f2a3b2784c456c3712be00597d1252b9b870195f7cfaf4e9114d9a3439106fed35a45d415d4d0b883187bb363c81ae6ed54bbe4ba44e17ca260924dae92851c77868456033d1ff99b4679a376ffef2f8f1e36be4ebbde87f993630cbcbcdcfcdcd0351c4e3f1e27ef70027a127f89f2d49088e9169180286802150a31030725ba386bb3c3a5bb63a070c1810f5d4534f758a8989b90482380022e9de612b822829a93674fdefb1a58c078fab53551944ded9d584cf437a9fbfe5965b5690093b625f687bfef9e763aebdf6da6e3b77eebc16afec20c86b1365530e5ab587bab2b4c4c0839e6b4f2124566f3f701e5e29280daf633e043999e36fb0edd1030f3cf0f99b6eba69e9c081036517c9b6054280b1c8cfcbf308abe207215061cb33040c0143c01030047643c0c8ed6e80d861f92180273372f2e4c90742262fc4c37a265edb16b416ce71b1e452c411af9e239790464728158a6052ce034156b92cf47e23fe2279af9e79e6994b478f1eada50152f1cb31c71c9378e595571eb360c1826b68b73f6d36a48c3fbfb8083a2e99fa9d0d3a86c8ea15632e5d849b8808d926c2f791870f3becb00f2fb8e0823f860d1b660f8e01486936c6380f808ddc96062cd3a97a0898c586802150e10818b9ad70c86b668310cea82953a6b48f8d8dbd00727a369ed796909a081f7154b83b3222931056115827ca971e9e3e453d1919199910d465e8bd9e9191f1066d2c9d34695296cb2cd891e7edd9b367ddd9b3679f405d4321a7a75047c3c4c444afd6d8925fa05972a02509d2c35e0fc4d8796e69575edd2cfab212afedc4dab56b3ffcc4134fccfcf1c71f3761475ec9b5594e7108783d1a625d2714976b69868021600818028640e91130725b7aacf6b766956d1f62187ecf3df7b49d366dda853b77ee1c00116d05c18c8418e2b0f31691409d14c9f4e553561edb65d435e9a28b2e9a88577819a47277621bd6a3478f86df7efb6d1f3cc557d2669fecececfa90dcb0e464ad22d8551bf6ed8a94b017a1959796f26ead2f758858ebed078b88bf9e9090f0e4840913165c7df5d529255461c90110e00a41efabd52bd8bc01d44aca32465c1232966e08180286400d45c0c86d0d1df88aec76f7eedd1bd05e7f88e0c510c95649494991849eacacac22c456e411bd229bbca51225e2ed957b4f65b249fb8d3adefefbdfff3ef195575e59bafb3200f2c268b7e1dcb973fb50f6ef90d3de10dc3a940b1349565b78915d7de497b851cebd128cb2ee6311b299b464cace83243f47c1676ebcf1c625b6be1624f6720b0bcb10b18da2f8de90db1cc6c63ce580679b216008180286c02e048cdceec2c1f6e58440bb76eda2bffffefb6e90c22110cb036826429e5008896fcd2c49ee9e74b1441302a9dbffce632a4548693eb299fade8c8a8a7aa379f3e6cb382eb2c61662eb6ddfbe7d3ddad5c719ae808c1e437b09905aafeaa3ac6b4b76a8ce404239d77e6464a4b3175db97c6751f67fcf3df7dc9bb4f53b1e63235700b3b75b64766444be373f9af27ffd3ff27a392cd5260fbe796f4b0595291902868021503310f8eb645233fa5baa5e9a52e81058b76e5d1d88e185d4d81ea208bf0d73649163e7b9851cba63854adb5d20b02e89b22e84a8c24ff39643545fbff5d65b8b7d78ac61c386f1cb972fef1f1111a1d77d1d4399581af6caf3ebf57a1db1a5bcabafb43bdacfcfc8c8d8415d53888f993871e287975f7ef926afd76bc4aab42096a0079e11defc7c796ef7e6ff91be6a66171725606bc98680216008d44404f6e664521371b23e971101c8aa17af6add9c9c9cb3886b694018acd411598590424732c973aff852f58a2b9440223d127959750c01d2f2803cea5b111313f32c44556b6c8b2335b5b66cd97215656ea08d6e8451d4e13cb6c45d9b0a29afa0884080dd31e55ce8db419231396f23c7afc5c6c6de85a776e6c081036d7d2d80846203f7a8f0b0f004c63f8201da55657ebe272c5cab15761d06d8a750be88e73e80ae65850601abc51030040c814a8d8091db4a3d3c55d338488af7f0c30fafbf6ddbb63360859771dcb0b89e90ee886d7676369c26dfc54568212b6e7dab08ad3cb70564338f7005f9637af4e8318dfaf6f098b66ddbb62144741804f472bcc51d693be8fc468faa3cae6df45d5c76b9c8ae1d7c3a6715d1f10d1a3478f8c61b6ffc15729bc3b16d2142002f7b544e6e6e12e31be10d0bf3483c5eaf272f3737580b9a035b192f7bed5a30a42cdf1030040c811a8440d87eedab355eed108068787bf7ee5d6fd1a2452711bf003904d963012569aeef22b08a404add7b6c7dc75eafd7adb3c5ebaab5ae59e8fc41de23786d3f983163c6068efd1b75793b75ead464fdfaf597c2442f494b4beb00610e43dfaf5352043d97255d116977c00e12cdde93c9ee176c7bba76eddacf5d7df5d5ab8cd8824888b7679e79262633332b11fc23f43507c6538bb04bebb9dd8ebe91db108f8955670818028640554620ac2a1b6fb6573e044e3bedb43ab366cd3a213e3efe7c48470fbca13184450c2d7cacb8bca79052a70391745e5ca58bd87abddecc8c8c8c5fc97ce6f2cb2f7f37353555c4561e3b923c9e891327861f72c821cd56ad5a75217917c5c5c575a42ccedf30e78df594e24f449632ae5d0aba1290e85422f391f10d1b367c152ff41f466c41a31cb62d5bb644e7e7e52672a1b16b1d42feaee1d598046b8ef9b1232f2fafda92db60fdb77c43c01030040c813d113072bb272696b217084044bcfa0ad88c19337a4647470f4a4e4e3e0ec29aa47424608df29a4aa130c9e4381fd29b41d925c89b37dd74d31bcf3df7dc5ad2fd5bb76edd222fbdf4d203162c5830303d3dfd62884e2708718414203cce13ac783011a1a52dff97c7d0df0951fe8ef4e720b46faf5dbb763575ef625c64da163a04185b6f5252522c73203e2f37373c324acf9551bfd7eb91179758c0cdebf56ea30e79d803ea59a621600818028640b543a0c40e19b92d111acb282d02f29ef6eeddbbd1ecd9b34f83105e01d13c1e625b07b21a06f1085a8d8828e59c1e2447611e9ed3ed78f2be81283f3d62c48837c78c19b3521912eaf4f6eddb3769fefcf947e1d51d46dae5e875213d8a76bdaa4b6495f4526d6a93b6a49b451dcbb0fb4d0e1ebdfffefbdf85dcae276e5b3921c0c550f4ce9d3b1b78bcde049af0666769050a31796fbd7bac6621638f6d0de39dbe47aa2518028680216008d458048cdcd6d8a10f4dc72183610f3df450936fbffdf642afd77b2bc4f664c8465d6a0f83781294bca1efde88200dea710f9111cf8b8a8ada48d9f7232222ee1f3e7cf86b8f3efae84a74f3c873db11471c913475ead43321b2b7927e39891d333333e5f2f34272dd1b194456a987acc01bb63a7dcaa5436a6760c77ff112eb555f9f10ea0d09812bb0dc7d42605bd6b6a4b08888d6519191f154e4d86c744c8c07b2ebd6dd7a82fc31fecb535252b484248866906ccb36040c0143c010a8360818b9ad364359f11d8108eaad08fa58c2e990cb2bf0801e0c594cc8caca0a93e714e251a251ca93c86b2b25e92b84d0eea0fcc784f2d87e3d76ecd8ade8f989ad74162e5cd88c70286df5c286fac42320a68e286307871e0f64d5bd47d71d04d8a97ddacea6dc57d8ff109edac9f7de7bef6f03070e346f6000dc429575f7ad7727c6c646b764cc63a9d31b1119e9c9ccc828ed7a69bd4163ed860d1b32286b9b21600818028640392150d5aa35725bd546ac12d9dba54b97f8458b16f586640e423a625a386413a79bd7bfde1562ea8ec92bb2494fc452a18829e4465f1e4ba69ecf517ceef2cb2fff11625b1cc18c47f734ea3d8cf2b8f83ccedb0731f5797e29eef140565d487d2ed48e328e002b2e8140eb380f2fefef1cbf05b19d85ec44cfd6d70248456cbffefa6b5c4e564ed388f088682f43999395edf180be2f0c60832e78d6e19ddf814ed07786a1639b216008180286400d41c0c86d0d19e85077530f73fdf6db6fbda857efb13d92d03dc84558a60d22a96501f990143d3cf605c475dc75d75d3777c2840969bb57d4bc79f35888f0403cad17428ae308bdbbebec7e0c01f6c4c5c5b964caf809306545c0f320c55bc89cf6cf7ffe732ac4d63ecc0018e5bbfd553be3e1657c9290464824c77f65068f89dcfe9696966663161c2bd330040c0143a0462160e4b6460d77683a0b0909e756b03ca717414a8ea356dd522628fb465d22b66990dae93131318f436ce7edeeb145c7dbb469d3b875ebd6fd0d323a0c4febc1785dbd84a56a1002e4f4a2a3a35d4859476c21d67aadd87bf5ebd77f01025ce44d0c4ed176e58ac08c1933c219f7facc2149592f8ef0ef7a56d6ad5b37bd5c8db4ca0d0143c010a84804acad902060e4362430d69c4af466848e1d3bb65fbf7efd20c8686f886722e291ec0d0a10ce8cccccccd94949494f5e7ef9e5b38b23b66ddbb64ddab265cbf1d47f755858d8a190d268081187c137f49d527c7cbc7fa9424e4e4e1ee91b90f72147cfdf7df7ddf3f1daca13e8746d573108ac58b1229679d312d9e55a2f5bb31aaf955bb76eb587c9ca869b691b0286802150ed1108abf63db40e860c014888f791471e69fec71f7f9c0b313c3d3939b9116925d65f380f42ba871e691910cd1fc978e68a2baef8e2c9279f2c728b99f25ebd1981f68e84000f87d0f6202d1ad15286523d7444197de1cc939aba8b03e1b515b1dd8407f843bcb52f9e77de7973870d1b968d0dd56dabf4fd61cc6b33070ec4d05d2e752265d872d05d3674e8d022738634db0c0143c01030046a3802466e6bf804284bf7bb76ed5afbfbefbf3f1572792ea4b11544d1796c39766159ea82d4e8c5fb8b2199af4150a6eeeeb1555d871c7248dcbc79f30ea1adcbd0d7e77cfd240872ada505520b28e1e1e19e8c8c0c3d3826c9834c6fa6c0146c7ffec1071fd4da5e23b60052d11b73c61b15159544bbed10ffb8120fba3117f496846414578d1f3f5e2497a86d8680216008940501d3adce0818b9adcea31bc2bee901b2c58b171f0b393c9f6a3b128623443da526b69012a7cf4eaf6e5a04c19948fc4308ca4ec222dbe8d1a3a3962f5fde09727a714242425f74632143ae2dd523725ba440090790674f81ae1e1edb4efc03c8f2d3b7df7efb3cf3d896005a0525676565e90b766d18dbb2aeb715a1d51b2ef49a38adbdad208bad1943c01030040c81aa808091dbaa304afbd9468866ccfcf9f3bb43142f8b8c8c3c223b3b5b0f90c131bd8e6c06320fa5ddb3f5caafd5109a77c9781762bb169d220485f6223efae8a3f6909f01e4f5ddb973671d74fd6f46a0acffad07a407ddd0cfc776bd1561226479fca38f3efa036d64052d584a05532b3b0203070e8c8f8989e9c0d8d4454afd7f88f9a0c632099711d9b5d684886d8680216008180286800f81529f547c052cac59084c9f3e3d62e2c4896df1d25ec4adfcde10ce448885239a78413dc4fdb23b32cac34bea3ea800817144383a3a7a13e99fa23b1582b96a77ef29696148db5f7ef9e502da3c0b698cae9ba7b44d74d7a6fa76c576ed7db628540a245681249fb8de85fa0e07e31f7ae8a10523478ecc246edb7e4460d2a44922b587318e419724305f3c5c54396b0bc6378b79f50b09b6de16106c33042a3902669e2150e10838d250e1ad5a835502018847d895575ed978f1e2c5a7432af4e184da18ee852c12784ae53dc53be7d6bc5238fa9ec200001000494441545e6576e0f5fd0c62f2d6030f3cf02b24760fefe99b6fbed90802dc3f2d2ded4ccab6a650c05bd6b245429d8e3c2ba48cb38d36f391ed10e40f2047cfd2de6223b64267ff0af3ca1b1f1f5f373333f3302c09486e193737ae8c21aa6ece694cd318f3c578fdcd73eb50b19d2160081802864061048cdc1646c3e24510a853a74ed2f2e5cb7b93782e64a20504c3cd1779500b7bd3c82f71d3c35c222890ce34ca7c8dbc0ac99c77d34d37ed414c68af161edb93d1390f52da81b245896d31ade4e6e67aa8dbe5a81d45f030cbd3a7e50fdbc89b06497e461e5bdadd834c4bdfa4e211602e35609cf5a60437a74ab2808b2197c57c706ba7636363b319f3f5c8eaa143876aedadcbb79d2160081802868021e04320e089c5a76461cd4360dab469f1c9c9c9bdf0b05d0c693c189211011911697460e07d731e35771078974fd92c08e78f9479f6c61b6ffc0692b9c703645491989a9a7a32247a30fa5d213fd1b4e7963f9057aa8dfa9d1e65456cb74280a6426cc78f1933e63bf3d83a682ac5ae5ebd7a895c747461bcea6250d03166ee384f3c653ce9e9e97a18f1b75ab56a6d637e1459ab4d5db61902351a01ebbc216008ec42c0c8ed2e1c6c5f0801adb33dfdf4d3bbe2253b0fc2d91db2a907c89c06c4d185221cbeb84b286107311601f90322f252eddab5a7df77df7d7b105b7d7d8cfa0e878c5e1615157504610cc7ce535742b5fe64eaf5c7650f1e3eb5978277797ac3860d5f1c356ad46c23b67e882a4584316ac2f81ece854c78308334be1a57e9512e9f72a9cc916553a64cb1f5b602c5c41030040c0143600f048cdcee018925f4eddbb7597474f459292929c7424e6b432adc436342066f9b02772ce2e10e02ec20c65a0a30130fea3bdbf9a38cc8a7bf44e7ce9da3d6ae5ddb96f6ae84d41e8b672e312e2eae54efb05525b24742bd3a9464429abe415e3deaa8a3e42596a74fe926950001c6cacb5c68c2387765bc835a84be272949afc3f568f9493e4437990b975f196fbde73668795330040c0143c010a8790818b9ad79631eacc75e48475fc8c7e92836874c8854f8972040769d4715d2ea082e3ac1b63578da3e4c4e4ed6c7138ae88e1e3d3a6ccb962dcd213b17e2213e93ccda905c4f5a5a1a5147665c186807892d9c9dcbadebb9a44d183a74e8971f7cf0c1ae8a0a6b587cbf22a00f736cdebcb90df3a82d86045d92808e67e7ce5dce7eca64226b495bd5b3674fbb680108dbf61302d6ac216008546a048cdc56eae1a978e39a34697200c4f50248627b4862b83c67848ed092e6d63eca932bcbf09e290824fa68c21775ead4f9a638a5d75e7bad09c4e5ec8c8c8c8bb9dd9c209dcccc5d6fe9521b6a5b6981a4900d7a8a7e11bacf366fdefc8bfffdef7fdb88db56c910e0e2a511f3eb502e9ae235c6c1ccd3dc938e42caa522cbcf3ffffccd8c7b913b00d23131040c0143c01030048480915ba1b0ffa452b5dcab57af08bc6a7f83381c8244f9c8259e5ce7b925cd853a565c64174f9a4b534744565406a2aab474d266c6c5c5bdb67efdfa4dc48b6c1d3a74a8bf72e5ca5320b38328d7d457a74f4975fbe22236be385e605fd44fb8b1258f767fa5cc04323f5eb16285de6b4bd4b6ca86c0ba75eb5a3067ba315efe57ca05b251f342f98422b3c9ccad5fcf3cf3ccad4a3331040c0143c01030048a43c0c86d71a8d4c0b4d1a347c72d5bb64c5f1f3b1fe29188b8e508c1a0c8caca721f69807438af2e9e39bd1d4164f61dea78e2b9e79e9b03f914317155d14e58ebd6ad5bae59b36620debbcb490cfa3a28482b6abb36b5b72be611b9a589fc1dec6640801f8448bf43fd1b7df916562e04faf6eddb60cb962d87e3a96f1f1b1bebc10b5b6a03995f9928af629c17242727dbc50b60d48ccd7a690818028640d91130725b76ccaa5d090861dc638f3dd6f3cf3fffbc05227a381d74f3020f1b514fc0b5b590590fde57bf0ef18d947bd3e3f18c9b3871e25703070e940797438fbcb9de575f7db5151edb4bf1c4fd1f1edb23c8d09b18bc84256ee879626262fc6de8180f6e1ee4585f3bfb00e273d7d8b163df4a4d4d5d4f5ff24aacc832f62b028cdb415c9c9cc278d586e096ca16c6597a7a906c1bf139975f7ef96fbb7fd54e0a268680216008180286800f0147627c07d539b4be158fc0f8f1e323c78d1bd72e252565201ac7e1058dc33b06c7f596cab30699f5a0ef81b478f0c6a542606650c7c4e79f7f7e6161624bdd7aeabd2eb7a54f85a80ea0dc81e8eaeb545ef4952df2ebc2c23b749d475864087dd716c6e5d3de76f4be8248bf74fbedb77f3b72e4c89d4a27cdb64a88c08d37de18ffe5975f76c0b44e78e22334e69a371c07dc1867e5e733ce5b28b3e888238ed05d01a599180286802160081802c52260e4b658586a462264c1fb8f7ffca3f1b66ddbfad1e3d3208feef3baa4fb89268491ace237e929071d2d3bc8484f4f9f037179f9b5d75e9b3b64c890dd9f668fc7d37a0264e532c28e78e122b92ded27b6aaa73811a9553ae4c6915c3cbe5a2ea1b7207c87d7f8a55b6eb9e51bbcb5bbb7a52226950881d5ab571fc0fc388439538f39e265aeb9f10c6622734b17349994f91d8fef6f786eedfdb6c140dbbb7c2b650818028640b541c0c86db519cab277e4fefbef4f826c1e8d07ed2c3ca94d21105e0884bf223cb1fe784911ca6aed6b2e1ed6e5e8bcd4ae5dbbaf060c1850846cca3b4c9e1e52fbbb42c86d14ed7a20b81ceeda203dbb2225ecb1cde54072f321b51be3e3e3dfe8d0a1c357105b115d9767bbca89006314f6eebbef7664ae1c86c85baf0b1491d6a006332ff2b9a04966cc175c7bedb5eb21bbba900a5ace140c0143c01030046a2e02a127b73517cb2ad5f389132746413a0e862c9e0979e818171717a10e40264456ddfa563c6d4a2a56a4a70ccae64254d7e2619d04b1fd68f9f2e57b2c0f18366c5803886c3f74e4b98d91d74e42395511507c649b761c19c2db9b4b3d4bfaf5ebf7c9c2850bed755f01d1ab34997519ebce484bc62f4263af71d59806b31032abb9b82d2d2d6d211750f6b06030c02cdf1030040c0143c063e4b6064e0288a9f7871f7ed0071afa42368e826c24421e1ca915e1c0b3ea11f92805347ab7ec56ca7fd2a64d9b7721b6c5ad87d42756bb416c86e07d8d435764c579ee14571bd8a3a058912d2238cac43679edd6109fdaa54b17233a005115b6efbfffbe031737ddb0359131f7320fdc1ce038e8c6d867313f5676eedc79051763d5624942d04e9b82216008180286c03e2160e4769fe0ab9a85f1a426dd77df7d27411c4e8330b6806c88803ac2499a231e8549a5d22018ee8d050a25a48968a651760e44f31dea5b521c1a8989897520347dc96b84b8b5bcb4a9a88bab2e771064471dd2d841f87683060da64074ecad0842a4920be314f5c9279f74e0e246af7c8b61de388b35bf5c24c88ef991ca85d6f73d7bf65c1f44d5b20d0143c0103004aa3e0221e98191db90c058752a812c443cf7dc738761f1d910533dbd1e49dc6d3ee2e10e0a769049ffda58df3285f070c785456e7f898c8c9cdcb469d3ef060e1c985550c41f88d8e0193e9484e3105788b04c9bdac76611ef1ce2d3ead5abf7ca5d77ddf57b992a31e5fd86c0ac59b39a317e5d21a80d31c27df18ed02d31511844f496849d8cfb8fd1d1d15b82e85ab62160081802868021e0103072eb60a8393b48682348edb99085c309e383f55c5ed68c8c0c3fc18d8989714fb94358d672ab794aab56ad3efff8e38f8b5dfbfaf0c30fb7a4fcdf68a30db2575b5e5e9e272e2e2e1b42fd23f117e2e3e37fc5f39cbd579559a10a45808b9bb02fbef8a21d174d87335ff47965a2015f69bcbb7d79787cd791b8ecc9279f4c252cdb66da8680216008180235120123b73568d8870e1d1a3969d2a4536263634fcdccccacef631a846e29427150402add5a5c48865b872ba28b5e32f2796262e28713264cf883f2f2e292f4d7d6bd7bf77adc7a3e1952731aa20f35fc95591023bd2056728067382b2d2d6d09debb0910db1f57ad5a55e44d0c2597b49cfd8dc02bafbc92c01c3888f9d19e8b1cf796049f4da4bb79e53b2e21cc65fc571c78e0815a5fbdc71c2ba18c251b028680216008940281eaac62e4b63a8f6ea1bec98b06116d4dd2dfd3d3d315ba6502100f924ade20256eadadf4148764e642787fc29bfaf6cc9933e7f7eedd3b67f7d203060c88fafdf7df8f86949e89b7555e5bbfbbae3484b6507d79d9d9d97fd2e63b841fa4a4a414f7c05a21758b5616041867efba75ebda7021752cf3c6bd3f59b6311f1494567218f7a51d3b76d4073b4a5bc6f40c0143c01030046a3802466e6bc80478f3cd371be1051b1e1313a3e50811845ac71ab4f7d1d1d11e48aa5b2309c9cc879cac81acbc425d5f77e9d2658f75b654e89d3a756afb0d1b369c41bc07faee6b54c4cbbac953b795423369eb15c8d24608b63d440620956fdbd322ee1044a6a6a67666ae744762993beeee00e3e8e6924a3037140492cca8a8a85fa8abb87916a89ce519028680216008d460048cdcd680c1efd6ad5bad3ffef8a3375eb0cb323232dc7b660983f61c32e9c9ccccf4402ef56603bd4c3f2d2222e2f5d6ad5b4fdbce5f71155c71c51575b2b2b206a0773a84c6adb32c4eaf1469e9e8cc419ef9f8e38f57628bc82e87b655760420b06103070e6cc9bc399eb9a007c9183eafe690335da49604170fb0d378ef6cdebcf9fc003a96650818028640e547c02cac70048cdc5638e415dbe0800103a2e6cf9fdf192fdadf211b892215221710cf806b1ea5274b156a7d24deb72ce29f127fefb9e79efb93b8c88754fc3274e8d0b8975e7ae9747425f569c73fbfd0f7eb9522224f9d48cd2beddab55b50dcd28752d4612afb0981175e78212a3c3c5c5edb6318f748cd35e684b3868b1e3fc955ba4b2c7ea739f00b771856159f6da9868021600818028640f108f8c947f1d9965a951190076dd3a64d6da2a3a3cfa31f5d75ec23140a219f7e82eb231d0a7d791013474428974dda62c257bb76edfa73716473f4e8d1112fbffcf26190dfb3297720fa51b4e9df28eb8f178e4082fc8705f11ccaeb53beef76ead469d6b265cbecc5fd7e843c953ec6387bafbaeaaac68ce131ccaf0e3a26f4dbcdfc7071d28b5d16533007f4f0e24ee6ed877880f758d3ed2ab09d2160081802868021500202466e4b00a63a249f7aeaa9f5e7ce9d7b3c5edb53209bb511afcf8306f9705d14c950a470e8d35108d915b9580d4179fbe4934f9e8517384dfa8505621b86344d4f4fef0b21e9896e9ccae2752bac566c5c7a78945d1ef17cca6fc296693d7bf6fcf8c9279f9487d8d6d93a74aac6eece3bef849f861fc8181ec35816b9c029ae07cc49978cae5bfec2dcd1da5c3870ce9a7af5ea7dcebcb2f17708d9ce1030048223601a86c02e048cdceec2a1daed1f7ef8e1d82fbef8e2d09494147d1dac151d0ccfcad29d5e8fdf5b4b9af39e4144dcbb6b7d4443e98a4380f360197a87edcc534e39e5e32bafbc72036945962350d6bb74e9d25a10d9e320a9a7656464d48710533ccc435c55152bd42312e3f268c367537a6666e66c1227f7e8d16379711e62f26cabc4083cf0c003f518c323b850e94c18d452915929696e666767fbe6c136c8eeb74c22fb5887c03131040c0143c010281302466ecb0457d550867086bdfdf6db07400e4e84741e4e18e72311b8d55c2744245c841d6aab1fcf0000100049444154f9bee5072e14f1941ef56412ff99f847471f7df42fdc22ce45bdc8b678f1e2c88913277682c89e0149d5275623445428e77f2abe48010e9447e0df68471f8950dd4ba3a2a2de3ef6d863178c1d3b560f94f9752a3a62eded1d0210da8320b6c7318e495ce404ad447341c23cf5e9e6321ffe642e7db27af56a7ba7b10f150b0d0143c01030044a8d8091db5243557514bb74e912377bf6ec6e908c5e900d7d91cc8d3324d579c60a135d110ba5ab77900ae7c15508f915d95c437cdae9a79f3e8bdbc37b2c4720cf7bf5d557eb69f8ded421cf6d9ceaf109edfba225866a1f62930f99d197a8de39f0c003bffceaabaf769458c0322a2d0283070f8e81d0f6605e1cce054e5869c61f5d7741959ebeeb5a86637d20e417cafec8dc287297a0d276dc0c33046a1e02d66343a05223e0484fa5b6d08c2b1302b366cd8a5db264c90910d8cb20085d200b5190045707c71e48a47f3900798e58a0eb48aff4247872b5ce71755c5cdcab10db4993274f5eef2ad86dd7a3478f863366cc388f5bc817537723088d1792ebb454a7ea7207bbedd4ae2f493a10f075d8a537233c3f7ffefcb5a4a97d9f8a855504818f3ffeb80de37e7a6464645dc6d0131b5bec87e98aed0d73489efe5cca2f650ebd3365ca940dc52a5aa2216008180286802110040123b74100aa4ad990c6b033ce38e3606cbe81f83184f11005788697e8ae8d7447644977eb6d950a31758417451d2abe1d82fb54fbf6ed9ff9f0c30f5791be07d9ecdcb973d4f7df7f7f0144e69ab4b4b4f684ee8b67945379578fda72916276ca73929fbf96f0f9e6cd9b3fb06cd9327b80ac18acaa4ad2ce9d3b8fe102e730bcfe61cc19f7f18f60b66b1ee2b977ef53661efe81e7f723c2affbf5eb9719acace51b0286802160081802c52160e4b63854aa681a9ed6a6dbb66d93c7f65048829608b8b723e00d7384362a2aca859007b7fc400444e442dd555c7a104db273dfa5fc4778514b229b7a88ec38ca9e09996d439970088daaf128e4d879845d42e0dd7674a7376dda74c29a356bb66283dd860e8c57a5cdedd9b3672c1739c74054a3cb622493cd3d78c85cd25b399652f68bb973e76e21b4cd1030043c1e8f81600818026547c0c86dd931abac25e2f09a1d0b493803825187dbfcce5d0b61749e5448a49f782a4d9d509a082d650a93d18de4bd75d249272d232c76ebdab56b33ca9d03013e323333d3b553ac62a14474fd476a8f832c6421f2c69f7ffeb996d0b62a8cc04f3ffd7438e677c9c8f8eb19302e7c480abe696e4072b5e6fafb962d5bfe7ec4114764072f651a868021600818028640f10818b92d1e972a953a71e2c4f0f8f8f80e1084cbf09c3681e43afbe5a97511b72b7e27822ba1ac14f228f37d83060d7e9a3a756ab1b7857bf5ea15b368d1a253d03b3e3d3d3d41850289888bf221dbee3da69493d7580fabe9430d1f1d75d451df43b6e5b5939a491544803991c05d8313a2a3a35b155cb8b8252f5c0095aa37ccbddcc8c8c895d431f78e3beed85aaa42a664081802868021600894808091db1280a94ac9b7dc724b3d88663f3c65474326234456093d3e921ba82f104b975d10a64142a743568a2518d4ebddb973671708ea69e8b7750583eca8cf69c81e88b7b3093bb721b321345f3cf1c413c5b6e50ad9ae2a20e09d376fdec15bb66ce981173f09a2ea3cf98caf1e100b6a3ff348770db64184171c72c8214b2eb9e4925daf4d085ad2140c81102360d519028640b541c0c86d151f4a796d376fdedc1572700e643149641212ea9622883804eb9e74282b22a265027a52fdeb49932629be47d1638f3d3661c18205a740a4bb4354a3c3c3dd33647be8154e10a9d5b1da505b1c67125f884c3debacb396da2d68a15375a573e7cef1ccb963636363bb32ff22446ad51bc6571e7a45030afa7a587115a478d6e0c183d73147741cb08c651a0286802160081802811030721b089dbdcbabd0520f3ef8606dc885be42d605cf997b600c02e9d6d7421482da221202c1c827dc8cf23bd75f7f7db16b6d478f1e1df6dd77df1d0281e9c3ede726b41946198a04ded073cb11a40be9d617cf56e3f9fdf4cc33cffc0e62be337069cbadcc08684eac5cb9b233173b7a4b42432e78fc7322d8dc53be04529bc27cfa917efe58a74e9d1442db0c0143c01030040c817d42c0c8ed3ec1b7ff0b4364dba4a6a69e161717e79e528740ba77d9ca32910785c104c29a0cf99c0de97cffbefbeed34bf4f728b276edda7088ea71c84148a4144a5b3f753b4f3224268d72df0e1a3468eabffef5aff594b7b723004855dd5e79e59548c6f638e660372e90a219cf5275a5909ec6ff4f2ecabeecdbb7efdae2be8057aa0a4da98a2160e61a0286802150be0818b92d5f7ccbb5f601030684fff8e38f3d20a7cdd2d2d2bc845abfe8da541caf988b07d9e5402e56a1f326c47639c4638fdbc2f2d07df2c927f1d4d90eef5c2dc88c1732e381d8502cf0263dd901e9cea71d3d44f65eab56ad96db7284c0b855f65ce644d49f7ffea92f919dc43c705e5bc6d86f36f3c81f2f1cd92d3d93f9b180fcef860c19924a689b2160081802868021b0cf08546972bbcfbdafe21564656535853076873cbaf5af104f0fc4d3f58a341706db413636a1f3d9fffef7bf39d75f7f7d490ff384ad5ab52a81faf5c9292fed968ad852afd30b0f0f97876e33b64db9f8e28bbfbde38e3b8cc8089c2a2a9058efcb2fbf5c9bb97712f3e730bae13cf98cb3f3d0735c9a4d1751ab293f79c08001ebcd6b5b1ac84cc71030040c0143a0340818b92d0d4a955007cf59cce79f7faeaf9075c3bc28bc678e5888d442189c0757a104ef983b869478a2a3a35d1c2f2cc53c29a4cdab55abd647f5ead55bab8420a2f9e23cc4aa3788aecb961eb6e9bda53f60c71b10a28da489ecba7cdb553d04264c9810bb6eddba6e8ca3bcb6f5195ff78604cd3de693eb10692ed40e3d373715573ec7f988bcb673283393f4922eaac8b26d1f10b0a28680216008d4480444566a64c7ab7aa757ac58d1212d2ded24fad10c09388e1008f7509708ad5eb2af1072a177cdae82707e3466cc980578ce8a7d4302757bf0b4e6d6ae5d3b2526262619fd3c796e4552e4a9537e20412f9f727a41ff1b279d74d2d2499326a9dd40452caf12233071e2c4f091234736634e9d0881ed82a91148c08d39e02ea8983b4e8f500f30ae62fe7c3074e8d08d36271c2cb633040c0143c010081102014991bf0d8b542a04f0da26bdf4d24b4743527b60589cc80361b11b04c479cd14e6e4ecfa56427676763e04752b24e39b030e3860769d3a75b6155bb82011229c7fd659676550e6b78888886d88f3bc42700a340206b9e9e9e93fc5c7c77f5cd287210296b6cc4a85c0faf5ebe3996f7a80ec68c2bac18c63ae3815e690273656ab5a3c22bae9ccbdefa3a2a2a6e3059657dfe9d8ce1030040c0143c010080502466e43816205d601a1085bba74e98178437b738bbf25c7413d67221822a2e87a28a75bc2d9d1d1d14b48ff74f0e0c12bf0da06f5a642447221c8df428c1740923321ab8ee096a2ebe9898989dfa4a6a6ae2f85aea9546204983fe1b7dc724b4be692de9ad1115383bee898b9829a23b41eee3478984779945fc5fcfb382525459f7a76f9557d67f61b028680216008541e048cdc569eb1289525e79c734e9dd75e7bed848c8c8ca3bc5e6f5ce142908fc2872e8e87cc85e8ba9072f29c6d85607cdea64d9b79788177ba8c203b79d8dab56bf7236d7c8eeaef90553d104434e09647fbbfe11d9e1550cb32ab040237df7c731cf3a63b17453db9c8a94318d46ec6dfafc3dcd117eab4bc651ee9d3fd191631040c0143c010a8ce085478df8cdc5638e47bdfa0d63b7efae9a75d20aaa7412c9a4010fc9e331107d5ec0b1597e06d755f8a527a6464a43ef29083f76c41cb962d3f7df8e187b516566aa592e5cb97ef6cdebcf9bbb4fd6e7878f81f84bbd639145f5ae4f74fdaff1022bca878154bad2a08307fbc63c78e6d8bbd2770c7a01d7328cce79525adc48df17779cc17796df399b32b98bf539913e6c977c8d8ce1030040c014320d40818b90d35a2e5549fc8c5d34f3fdd885bb927422a0f83584441125c6be4b9b0b89d4805fa2e0b6f9b1ee4d9919595f5f6e1871ffe4bbf7efd325d4619766bd6ac59d6b871e3e7f0e03d840ddf50541f7dd01285c2a2a7dfe791f7bfa4a4a49756ad5ab59db86d551881d6ad5b4733cf4e65dc8fa61b09cc21f7e60de24137cd3fcac96bbb0d52fc7d6c6cecd7410b95a460e986802160081802864010048cdc0601a8b2642f5ebc3872faf4e95d21abfd20a9b508dd97c8201c014dc4532652e1913e242387e34fba74e9f2055ee0bd269cab57af5e0e697d9dfaeea4f13b90f1c87b053281f0dfc8edc80b3b76ecd0871b88da569511d8b0614337086a1f2ea80e602eb9577fe1c10dda2574dd3c25cce3cec12f19191953efbdf7de0d410b9a82216008180286409911b002bb103072bb0b874abd87c0869d7ffef92db8c57b3204a33304238cb088cda4b9b722285486420944d879d8282b92b10172fb1ca4442fcfd7b201a99659a8371fd2ba6df6ecd9f2c03d47050f21774ba2a3a31f247c06f23c03bbd74b9763dbaa300275ebd64d621e9d171313d30d8f6da4e692bac3d82a08289aa7786bb534660375c8d33f77d8b061f6868480a859a6216008180286c0be2060e4765fd0aba0b28d1a356af0f3cf3f9f01a9389726e37ca4c21792a687c414f80562e94f8390e471bc06cfd9c375ead4f96ef9f2e5655e8ee0afb82042dbf9059fd0dd41ddbf217ad8ec473c73bfa1b27de0c08159d2216e5b95446097d1bd7af54ad8b973e7df997bfd21aab595ca05928252097340770e7640703f898d8d7d7beedcb9b6d6b654c89992216008180286c0de2260e4766f91aba07290c6882d5bb69c86d7ec4a88c201100b027757d84f5e7da6a0bb471aca24e76fc2a3fa4683060dc66fdab429c5a71faa506d207905a2b5b7a1aadaead98f08f4eddb377adebc7917406aaf6112b5cec9c9f132fff69863814ca49c2e72667281f5527a7afac2820ba240452ccf1030040c81ca8f805958a91130725ba987c7e3a95fbf7e3bc8452f3ca21d200a5e3c68bac5ebacc613ebc2dd77e83902a2303c3c3c83fc9f29f7f69f7ffea93887b61902811118306040d4d4a9533b2427279fcd458bbe82e7d57c9230975c61c55da4e49d2e74d6e1b59dd9aa55abc5e8dbfc2b192bcb31040c0143c010081102466e4304647954d3b973e7a8d4d4d463f1961d4bfd11887b304c21b7783dd9d941972ee6e36d5b1b1f1fffe909279cf00b2465afd7d9aa4d937241a0d2550a09f54e9a34a97e5c5cdc40e6cc111818499a5bd3ad90630f735241894239e967a1f03d6478c6983163b69226b24b926d868021600818028640f92160e4b6fcb0dde79a21106df1d81e07396843dc115bbcb8ae5e6ef1062518286a09c24f10e4cf860e1daa3849b619028111183870603c1eff23d3d2d2fa31efea33ffdc3a18c8a9bf60e1b83fb1205290a70b29adbffef098638ef9953a45740b342c30040c0143a0b408989e215076048cdc961db30a293171e2c4d86ddbb6f5a2b11e785ec320186e390264c3e35b8e20121140b4067615deb7cfce3efbec55908b5ceab2cd100888c0f8f1e323a74c99d20e727b168a5a12e3ff1fa10b2bdf7c539cfc3d36e593280fed4ee2fa0ad99fdfdcbb0000100049444154f4e1c387ebbdc724db660818028680216008943f02fe1357f937652d940581175f7cb1f39f7ffe790224e3003caf1e48aa2baedbc25a8e101111e1c8ae4bdc732772b11d72f103ba73478e1cb9634f959a97623d0e8c0073cbfbc1071f34ccccccecc3c5d431cc9f0495600ebabb068a4b4857e0d11c74913d77fa72dd2fd4f171dfbe7d37d885d59e00598a21600818028640f92160e4b6fcb0ddeb9af19ec57df3cd37c7c6c4c41c0e41705f22d332041fa95028cf9942110f48897b80ac505c2fcc5fc5f18ca14387aeecddbbb7c8c65edb63056b0602279f7c72d2279f7c72447474741fe65773e695fbff40dc7f21459a8739e900c9c9d935ada2a2a2dcb176c4f5dab90dccbdcf399e3d75ea545b8e0010b6190255000133d110a83608b89357b5e94d35e9c8e8d1a30fc4e37a4c46464653bae4d63b1216bb8960c8830699d0471af4108f962decc8cacafa9e3ae69e70c2097bfd25b2621bb4c46a89c0f4e9d323befdf6dbd6cca553b890ea0a898dd1c553693acb3c736ad2c7eb9b491d3f90308579bc95507711086c33040c0143c01030042a060123b7158373a95b8110c46cddbaf5180846370ac52201c9ad48ad3c6912910b3c67b9908ddf09bfbcf9e69b57ef975bc2186d5bd54260c8902109cc9f1e5c501d4dd810ebbd105c82c01bbaeeae0184d68568afe482eb93638f3df647e6b21e2a23c93643c01030040c0143a0e21030725b7158076d0932e1bde79e7bdae3fd3a01b2da248cbf608574cbd84730a44bd964c26f626363bfbbfffefb6dad2d60d816148188356bd61cc445d199ccbb039953e1be87168395d4c5957420b47a7b47667474f4ccae5dbb4e99316346b9bdd356bf139fa86d1343a0aa2160f61a028640f92260e4b67cf12d53ed37dd74535c5656562f88c59190d3688846d0f2e2bf3e3dbcb5f294ad6ad0a0c147d75d779d7de634287aa620046ad7aedd1c523a18727b0473284669c4150415915acab8e530ccc37590ddf7cf39e79cdf83162ca382c82c9e602dee6d909494d49e36bb2624241c141313d3a65dbb7649e4dbffb232626aea8680216008545704ec84504946961377c4a38f3eda86dbbbc743129aa4a7a787c9231bd83c8f2315100a0f7ff9f2da428cbfe8d3a7cf5cea4b23cd3643201802e1a9a9a9a722274110ebfb48ade61473315859f7c6042ec8b424219ff9ba00b2f90d734f175941cb965661fcf8f191d4dde2de7bef3d1142fb7ffc3eeec2ce47323232c6913e7aeddab517d5ab57affbe9a79f5e873e045cc653da364dcf1030040c0143a0ea2260e4b6928cddf6eddb13f08289d81ec16de1083c53bacd1bd43a74dd836428e6e1c55d51a74e9db77af5ea65cb1100c4b6a008880876862c0e8e8f8f6f013174738e0b24f77604e663d00aa483d7577aa910cdcfb76edd1ad28f854094a3860d1b76386d8c84443fc245df2d10f173b0f104ecedc5f18569696963487be08b2fbeb8a87ffffead480f9741268680c72030040c811a898091db4a30ec7a52fd89279e688529c742349a4312bc9cc81dc1202de8c689df0319d65b1166b46fdffe67c84076d042a650e311e8d6ad5b121eda6110c5ce90c370e69d3cb01ebca20e1bf25c1868a732dc31c8c3cbfb2373f73b7443e6b585a47a21b76db0e31adab988badb7331174b3c1c52eda53d2f79114802bf97a3b0fbaaa953a75e02c13d4065d1b7cd1030040c0143a0062260e4b674835eae5acf3cf34c1c27ea6ec86190049dacfded71927684a370e8cf2482b75624389f93fb8a66cd9abd77f2c927a7926c9b21101001e69377d3a64d0713f6819c264218dd3c630efacb4124ddb20325689e2994282e7d48a5efe30eeb209b1f93b70209d93672e4c82888f7f1547822f3bb11b6baff5784ce56d235f73db2199b22b1c17d59ede38f3feefdeaabaf262adfc41030040c0143a0e621e04e1635afdb95a7c713274e0ce744dc1c2271141ed8033841fb9619c81b1bd4509dd851da0109f82e2e2ece5ebf0418b60547e0c0030f4cd8b871a3d6d936435bcb1308f6dc341f95aa792632cb1d024726453099b3229719ccbdafb9289b317efc78dd3d907a48e4b1c71e8ba75dd9d890f6f6b051041b52ebd69dcb161ad503676d54e6c9279f3c8063db2a0c016bc81030040c81ca838091dbfd3c166ddab4898e8f8f3f1c33bae1418b1181d0091bb2e0c15b45f29e1b277a7f227a799cd8572626267eb064c912bd06cc9f671143a03804468c1811bd62c58a8ecc1b7945dddb118ad3f3a589d02a8ebe23b69aa32296a4e73017976567674f8504ff12cae5307dfbf68d666e1f4a9b474156236847263822abb6254a20cfd9a4b884f4042e120f9d356b962d4d1020268680216008d440042a1db9ad4963c009db3b78f0e0261919193de977473c605e4ee6ee644d1e49256fbe7c484526f11f201cb34ad6b61c43601702a3478f0ee36e415348e06910c3cea4ba87af984344836f9a9f12e973f1b593129f4026bfa54ec539dcf7ad57af5e11df7cf3cd01d87705b5c9b34ce071c4d653c29f2e0895c56f293c2d2dad3ef12653a74e952797a86d8680216008180235090123b7fb77b4237efbed37bd1de108cc88c30346e0d1e77375bb37e0c95c8a2218845b6263633fe316b3adb5050cdb02233076ecd85ae9e9e9c73277ce83a436403be897c820b16e2e72f185faae0daf6a3aa4762e47535e7ef9e55521fc129e77ce9c390d535252f44189b3a93f0271ed2bf409f6fbd7dd2acd476e1547c2b1ad36613452dd36eb8f216008180286401004c282e45b763922909090d0054fd37978d10ee316af5b53a893341e2bd7aa4ee02e52f22e97ac59908e4fa8239fb86d8640b108309734bf9a70013588f9753de2bcb6a417abef4b645ef9a2ee2d0ae8e733df7670c7e08bcccccc07f104cf84d866f995f6213260c00079913b52f748ecbb95bb11c52e99c0863d5aa18c23c090ee7c2407db7672d11712bbf668cc120c0143c01030042a2902bbcc3272bb0b870adfcf9d3b3712cfd9599ca88f852c44ca3b262388fbbdb6858985f2761391d96de87f366fde3cf3daee068e1d1645202929a96e5c5cdc59dcb2bf0aafe8c15c444516d528fe88f9e93298670af329974adad71c3cd1ae5dbb9990db1ce2fbbc51671884f640daf93be1e55cecd5e6f721425ea46ef48a1c638fef8d0dce934b19786ece9f10dc15bd7bf7ce28a26c078680216008180235020123b7fb69982fbef8e203f1da1ece49b89e88ad882c9e2a0f1e2767914ee24a7307c5efe4b5fd054fdc2f908c90108ce29bb1d4aa8ec0c30f3f1c0ba93d9a39753ee451ef8d0d67deb80f3604eb1bf3d3a9401aa59fc3f172d8e3940e1d3a7cbf6cd9b2907846b1cb8bf7b7c9871f7e783275f7e5b80ebf09bdc3d6b5ad1d698ebc2a5e5820c2fe747e3f22df5bc99fd5b56bd77dfe0430f5d8660818028680215005113072bb1f064d1f6dd8be7dfb71347d202773e741d3c95b048234ffa613b708aec497a8b884e39d108d1fce3cf3ccdf3916d125c93643a02802785623fef39fff1c8827f44208ed21ccb158cd2b69f942c54b12e6a76f0db8ee146ca0ccb4f6eddb4f1f3468d056e69dd24a2a5aeaf4214386d49a3c79f271d4f737086a2b6c8c888e8e7677304a53897e3b9415c94d477f11f2e9881123d610da66081802868021503e0854ea5a8ddcee87e1b9f1c61b9bae5fbffe28bc68fe27c14b3243276e88893f5bb76149cbe764be0ae231f71ffff8c7367fa6450c814208304fbcf7df7f7f632ea4cee142e804e64b2dc8a948a0d38248ba30d80e52ac5bffe9e87fd3a851a369e3c68d5b01690ec997c8264e9c18f5c61b6f1c840d67e2ad3d0c1b6389fbef60281e486263635d7fe8abbccabf438ca79e7cf2c97321ccb624211070966708180286403546c0c86d050f2e2761af882de4f42048833b9107320142e1de770b11766a9cbc156642721712998fd8491c106cdb1301e6581284b1576262e2809494944668b85bfdcc1da29e527b46636262f29977bf50df07ad5bb7fea95fbf7e99ae827ddce9b7f0da6bafb5c8c8c8e8cf3c3f06e25d9b0b399af16a0984134f90bff4f4747d452d3f2e2e6e2b367ed1a74f9fcfafbcf2ca0d418a59b6216008180286403546c0c86d050fee99679e99b06eddbae33899b72e4dd39cf0dd53e01061a70ec9d5bac2359cc8e78c1a356a4defdebd6dbdad43c676851168d7ae5d34a4f41048dfd59999996d21927a3d9623b49a533e825bb8ccee7158a64bcac8c8d886fe3bf5ebd7ff8abf907d850c0f6bd2471f7d74627c7cfc1990f0c63416c6bc769e58d9e86b9ff4801bba59696969dfa3ff61f3e6cd7f193870a02dd3098898651a0286405542c06c2d3b02466ecb8ed95e978060787ffae9a7ae9c840fe5f66b2215edf13438694536cae896b04b83d8ea754c9ccbf31611ff0e2f5a8acbb09d21501401ef860d1bb476f5cae4e4e46e10c788c2d91055ff9c2a9c5e38ce1c7587dcf6cfe5426c1ae194471e79e44fd243b2ce96cac33efbecb30e4ce6cb535353db426a2369c7916f85e4bbb8c220924f1d7f2425254d3ce49043be7de18517ec4e4610c02cdb1030040c81ea8e8091db0a1c613c4a616bd6ace9c589bc0304231c09da3a24568456b75e3d1062ddaadd1a1111f1fd7ffef39f15a1fcdc6950434ca1aa20e0ad53a74e0b2e8ace83f49dce6d7ef7c0a28ccfccdcb59ac037a74a9a7f1058a94bf2b9edff33f3f5e5060d1afccafc0d9947343a3aba1ded5f4b78280d451227d8b5a9fd8888227c7c5746f1fb6ccabe78c001074c9f376fde8ee2552cd51030040c0143a0262160e4b602471bef576d08c761908bba845e2468eb22b45292370bc292cb897c1127feafdab66d9baa741343a03002975c7249036ed1f74f4949b902525a1bafadcb66de7820bace639b99b98be48a44bacce277f2d0ae23eb7f7845bf5bb56a55c83ca2cd9a356b4edb1763d319d81acd7ca699bfd6006bce97e6b741217995df41f7fd4e9d3aade5d83643c0103004f61e012b596d1030725b8143f9faebafb7e644de2c3b3b5b5f6292173668eb105af72a260889d6daa67022ff69d4a8513fe3450bc93b46831a600a550681a14387d67ae79d774e82c45ec43c6b0e79740f9041243dcc1b8f882e84d7cd3b7402ddf6cfa3d31b28f70ae1b41d3b7684ec8d1c871e7a68edf5ebd7f7c29e4190d824eaf7123a9b88bb0ddb9dbdd8ef8e4bd8c98bfc13fd99d0ab57af6593264dd27109aa966c08180286802150931030725b41a3ad4f8b720bb60d27e37a34e9d5899b133cd1e01b64580f95c94bb512c2f1eddab56b938397328dfd884085377ddd75d7c5bef9e69b4771113400127b108431521746cc37f78056618334efd071499a878a303715482896bf83ddd7e4bdd7ad5bb790adb38584c62c5bb6ac3b77212ea6a196887fc3de3de2b2d39f4844849c404b74446457137f863a7f9c316346c8bccad4699b216008180286401547c0c86d050de011471c11c7ade23690865a9006b7242132d2bf1cb2442b2002cacb877c64407217b46bd7eefb091326642bd1c4101002a3478f8e183f7ebcbe78771604b02704378e505941c547202923d298cf7cd387107e8e8f8f7f1762bb68debc7921996bfa700904b60bf3ff5c88750f2482df4150fbb898d3859d5b4e4119e9eb01b2cd7877dfe5e063eab575b600619b2150b510306b0d81f245c0c86df9e2ebaffdaa6be3c60000100049444154abafbe4ae264de9684044ef0ee84cdc99ec3c01b64430a799cd8d74386e79e70c209f6e5252162e21080d886dd77df7dcd333232ce605ef5494f4fafcf9c0963beece1b1750576db71d1e44f613ee2e8cdfd1d623be5c8238f9cf9dd77dfedf467ee4304bbbcb7dd765bb3d9b3679f829d27318f1321a7ee3710ac5aca3a3d915c7e3f22e0c910f26fb175127743d692aeb5c1c1aab17c43c01030040c811a848091db0a186cbc6a91d3a64dd31ac8563417ad133627f852910f9114cae470425fd2a953a739e6b5058d425b4d8e328fbcf3e7cfaf0323ed939090703684af2569111c7b0a93d64018c963ab7c3cbdfaeadd16e6e517a9a9a9d3860f1fae87c994b5cfa2f7d962e70910d1bf41bc9b7307220c5b03adf92dd22673dfbd2984be6522bf40be2761dfc2499326d9baf32248d981216008180286801030722b14ca59b2b3b393200d87202d38c1bb87c9b2b24a7d5ece8f8989d1fb6c171e72c8213f97b3a9567d1542a07ffffeb153a64c390ac277c5ce9d3b3b4102a3e4115517441e150693b8b838a9e4331fd39867b3f08e4e3ee38c33960c0cd18710e459fefaebaf0f4b4b4b3b8fdfc1c1d815c56f406dbaa5062e1260e723e9d8964b3f5743dcdf837ccf78e28927ec6d210170b32c43a000010b0c811a898091db0a18f6575f7db50e1eb26edc326ec209dab5a8133cde32170fb4432f9f5bb9bf75edda75eecb2fbf6c27f44060d5a0bc5ebd7a457cf0c1070733afae81d41e4ed7a311471821a8ce2bcadc5152408174eab67f1ea47831f3f38d134f3cf13bea4d0b58a80c99efbcf34e7b88f30048f451b4114f516fe1df00c70137fae7de16c26f602b44f7137e3353060c18a03739d8728480c859a6216008180235170123b7e53cf613274e8cfde1871fda73623f10ef934eee2213ae554efa2e0cb48308e815608bb8ed3c37905eb9e75903950a8159b366b587c45e8951c77347208ad0bd4e0ba2eb5ea3455aa996bd8800239bc2c3c35f3ceaa8a3be9c3a756a48d6d9ca9e0e1d3ad4fff9e79ffb51775fbcadf5f0da86319f9d9dcac70bab20a0609bfa23b2fd3d44f783abafbeda5efb151031cb34040c0143c01030725bce73006f5312dea6c338311f00997578eb84ad93bcc260cd4360d6c5c6c6cea7bcbda43e18583524bf6ddbb62d208c83991b6713c631373ccc119140f7353b11dbecec6ce7f10c0209d3303f970baf17d1fbb86fdfbe9b0843b2356dda346ef5ead5c751d945d8d982d02dc7d19ca7510e4bb751360712bc9c0bc3b721b6df8f1d3b566f73285d61d332042a10016bca1030042a0f028e6c551e73aa9725786dc32fbcf0c2a6dcee3d9c937423bc6a9cdbbdcea346c41faad73a96ec16d73ac31f9a356bf66da85ec9a4fa4daa2602ddba75d3bbe35a6fddbaf5027a30084f685d889f97b88739a6c08988ad220a35a7987b3a2c427621b4ba23900a397e87cc572fbbecb23f468f1ead8f3770b8cf5b3c36f6a4dd2b20b2077151e7ff962ec76edeab05c5659fe23ea18c8b629fc27cfab7818bc35739f8ecf1c71fdf4a689b216008180286802110100123b701e1d9b74cc84512723cb57481d8c612fa4fec8a4b748257a893faaeb8c7af131717b7068fd51778eae64bc7a4e6223074e8d0c8050b161c5ebb76edebb66ddb361c32abb76f0405c437a734bf988b8ee04268f5f6816ce6e45790c707ce3cf3cc25a17a0b07f6d5c68b7c2af5de9c9a9a7a1206bab7831006dc64933cced8e4def4007197adfa38c3bbe4bd3a6ad4a83510e1fc809558a621600818028680210002466e01a13c364885f7a28b2e6ac449fa58e480dddb20df2571c2762164c0ad45d4b14ef2e4e7a5a5a5cd8694681d64a653b25d8d45e08f3ffe68c61c19c29cb814105a72d1e39621100fb8410c9d9e48a3e695082ef5e4464747ff8147f485010306cc0fd52bb574a7022fedf110ef6b68e3440c8b611e1394bcf9f299e76e4985340b9659e463eb1ff5ead57b89fafe0ca157594d98546504cc7643c0103004822060e43608407b9bbd78f1e2c8c4c4c44e9cbcdb5087f3da1206dc748247df9de4f17e6daf55abd64208f2ca80852cb3da2300010d9f356b564f086a6f24910b206f46869c9ac1bb0ec9f42b15c4f3985beb21905321a2df4e0ae1bb62870f1fde10f27d01f5f7a4d170ec2408bc152c3f70735e9a3a86787bb06f27e4fb2d7e430b956e6208180286802160089416819a4c6e4b8bd15ee9411a62929393bb53b831e2d645127a8a3be18bd42aaf807c28aa57392dddb163c7cfe3c78fb7d77f39446ae60e8f65d8e4c9933b3237fa814033ee02b8370e40fc3c12d24ab58930428cf3218fdbf0847e49217de16b0361a8362fb61d8547f904ea8fd13cd7855ab0ca29e354d41789ca406c930967f0bb787ad5aa55a563f1ae16db1902868021600818021e8f91db729a05f7dd775f1d4ef05d90ba8823b50ad51c276e054e142f9cae133cc7fadcee7cbc56bfa0948bd8560311d06dfe7befbdb72d84f4226ed19f40180b7974cb57f0ba7a24c160612eb9b90761946a2a84f13b3cab6f0d1d3a74011760a5fe92880a0712ee32d486d4f6c3a3dc407a9071b77656f140429f5c36fd73fda19cde86f013bf83a7a9ef0f9769bbfd8480356b0818028640d544c0c86d398c1b84d50b893898509f4375ef2055331cfb1f16d37161f19de40b08cbe68484844537dc70c37ac8893d445318a81a1267ae843ff8e08307100e642e0d84f43541c2208f8edc322f3cbe39130812cafbf432c3c3c37fa6aeb72fbbecb26fb82310b2f7d9429423b1ed24e4d8c8c8c888c848bdd4c1a3a505814c73a4dbe7b9959dd89603a95d46f93720e35fd3479bfb0111b44c43c01030040c81e210a810725b5cc3d5396de0c0816190d463395937a29fee36b24edec48b6c9cbcdd091e52a0a7d75d9c3454f3974062960e1830c06ec91641ac661c3001bc871e7a68e3850b17fe8d793490b9d08a39120139f588d08a10a2e3e64c3044a48f573497b9f83bf177a8eb8bc68d1b6fd63c0b56b634f9d8e1fde8a38f8ea0bef39156b4e545dc97d2029547b74836a4560fbeadcbcbcbfb98f2d3468f1e9d5c44c10e0c0143c01030040c81522260e4b694409545edb3cf3e4b808074e3045e3b5039888123b4d28178b8386979909805c8f2ce9d3be728cfa46621d0b163c77a4b962c390d92771173a8b3882da1d661ef4168952e81bcbaf923a43497144ac893f7536b6bdf666e7d889735646f1ea03e6fddba755ba4a4a49c83a7f558ec8d519b126c56e0041bfcb6b9047694f5dfc5888d8dd59284ed24cfc2f629fcad81dc86ea9dbb545be93733d01030040c014320840818b90d2198beaae2e2e23a116fc709dcbf2481e362373c55ee363364589e2b11918d949fffd65b6fad8314d809be58d4aa6f6283060d1256af5e7d0af3613072043df57f0081f81e1b73cc4f121517c9d59c92224451afd312699ccc9c7a65f0e0c1cb43f53e5bdaf2d6aa55ab0e9ee0fe126cd55a5bafda2d2ccc61671ffa8ee072d1e6bcba4a97e8383d3d3d233232f247bcb7ef52d7827efdfa6516aec3e286802160081802351981b2f7ddc86dd9310b58026f6bd4b66ddbfaa0a407caf638d9935e64d303423e3282072b0742f2737272f2d2264d9a641751b4836a8f40f3e6cd635353537be101bd9cceea4d1be184fe4d04d17f5028121f1fef08a492a4238138e64338d3984fd321b64f0f1a346859a888adda1933664c82bcb559595917206d98c37e5b69db1159e929aed027b24d5e5d8512c8b81e985c459fdf67cecf244d64dca76ea121600818028680215066048cdc9619b2c00520139169696947474747c7e9241e58dbe3c153a52f3139353c58fa6ad4c2ae5dbbfed6ad5b379df45dbaedaa3f02ba28dabe7d7b0f3c99c3e9ed7190c2225e7f481fc97b6e783c3d106297c1dc732457ba88bca1df71f1f4d8e9a79fbe2094c476c48811d1b7df7e7b676cbc08527b386df9bdcba4395b487321f9eece0424d67fec22ec489367599fd49d72e081074e79e0810736525e772fc82d9fcd6a35040c0143c010a8fe0818b90de1184f9c38311ca2d1842a0fc46b1629b2413ce00699712fb087846809c2764eeebf421c7492d771c0b296593d10e0422672e9d2a55a82f00fbca1bd983b517832dd458f48a2a4a49e4acf974739dfbb6f33f1d62e24efe99494941993264d0ad98512b68441949b43aacfa4fee3693b96394bf0d786ce5f07c4742c1d851c3ab2cbbce7303f9ddfc82cea9a347cf8f0df070e1c18323bd58e89216008180286409910a836ca466e433894e3c68d8bfbedb7df8ec36b9b20afadc846b0eaa587074b1edc6c4ef8cb38fe6df1e2c5f6205930e0aa49fee8d1a323e6cd9b773873e65ac8622f6ef1c744454579b8d871173dc1bac99c712a90444fecae07b3b2994fbf7247e0b5faf5eb7fe43243b4838d7a6fbae9a6fa90d153a9ff6fccd586a4396fb1af091dfbe2be5069e8ba43915c091edd5cd27fe137f272f7eedd178d1c3932a4eb6cfbf6ed0ba4d1edc0f1441a3e0f4c2f4c4848380f9c8e3ffae8a31b92669b21600818028640354520ac9af66bbf742b29298973698c3e939ac049b45436149ce8752b361302b0e89c73cef90dc2635edb52a157b595e6ce9d1b3965ca9443131313af86d8f681ec25421cbd105c5dec380f67b01e32679c0ae53de9e9e979945fc1247c0bf2f8dee6cd9b43fa3aadabafbe3afea9a79e3a0eeff2b9d8da11c258f2ff0f6795a7481f34d729a7b73e68beaf817dbe09b19df1f5d75fa714a8ef73307dfaf488534e39a5c5d4a953cfa2feeba9f0aef8f8f807c0f441bcd863f85dde397bf6ecc1279d745267e9926f9b21600818028640354320e8c9a99af5b75cbb8317ad361ead43201ad13ed211ac419decd1c9e7c4bf837049d7ae5d3710da56cd1158b46851d4d0a143bbfef2cb2f5742bc4e858cd626f4fae60d244c6fcf28150acc1d3dc095cffcfb9db9f75e5a5ada7be3c78f5f5baac2a554e2822bec85175ee88457f84c6cd33adb28ecf5972eb0c17f5c5c04fb7cc95bf1324f6dd8b0e13bfdfaf5db4659915d5fde5e8722ab77dc7147ebcf3efb6c20f55fb773e7cef3c1a35b6a6a6a4bda6e8ab4c266ad6bfefb8c1933feeff6db6f3f828b8be8bd6ed00a1a02868021b0970858b1f245c0c86d88f07df8e18763bffbeebbf69cfc9b205e9156c2a0b57362978e6e25afe6e42bafad7db84188546311b1bdf4d24b0f5aba74e92510d17edc9aafcf7c61ba84b95bfc9a13a439041457c417a2e45ea5a534c50b425d1cade3f8233cb7ef4244970f1b362ca46fdb78f4d1479bd1d65990f013f01027315739fc6bc37e67fb5f29bb62e8fbbdb7ea0336ea0d0e732099af416effc0d690dca59838716238c4b605bfc1feb4793117998762411d2e16f4609ebb6820aedf650c616b6c193067ce9c21a3468dea220f3abab6190286802160085413048cdc866820172e5c98c06dcf8339b1c653a5172995e78d93bd54b32007cb213c2b7560527d11907771c080019d972e5d7a31b7f7cfa2a77a00d17dc58eb8db980b2ed44e7108ab9f3832bf94ecbe54a6787878b86ef36f876c7e8aeea47beeb9e7670863482f90ba75eb1607193d8386cf628e37a3ad3008a2b381b4a01b44d2e9e245cdc1e65f21ee933a74e83067debc792121e0f4db4b9f1b40524fc5ce81e0d599b4687e5bee7758d840d2f5bb8cc0fe8af17e9a00001000494441548648df5f7ffdf5147e77f548df43b770398b1b028680216008541d048cdc8668ac3871d7e2a4dd95ea6239a9fabd6b1c07dc38c1ea966c0627fde50d1a34581d50d932ab340210a8b02bafbcb223c4f632e6cb40a40569e18823afbeb0b84e6a4e293d2a2acae932d74418e5b14de516fc4c08e74bf7de7befbc9b6eba29557aa1124863d892254bba43502fc41bda917afdefb3950d1c07dc282732a92fabe5433ad7c4c4c47c9494943479f1e2c559010b9621b379f3e6757ffffdf753287231186a59104d45ba76492b76432f8cdf5c33e4786ce9844416ab688986802150b51130eb6b2402466e4330ec43870e8d7ce38d37e409ea08c988d6499f9366a96ae6e49fcb097f3d27fcdfc68c1913d207804a658029551802b56ad56a0b091b0af33a9f39a2dbfcce630bd17236f8427750b0637eb8b726f8e693741487ec12cd4fa79e591c4fe0f6faecf220b690db563434343939f970da098f8b8b93a7d85927db5c24c08eb222e3ba80db0a99ffb871e3c6afedd8b1635b802265caaa53a74ead2d5bb69c9e9a9a3a223d3dbd3be45f6b68bdb4e59643c8c6dda55003c2bf037574024723b78580b1a8216008180255190123b72118bdba75ebeaed08ed39f9d7e344ea30d5491d0212ac76d4f233f188fd3270e0c015c1942d7fbf22b0d78de3598c8d8e8e6ecf2dfd6b18f0f3a8a809644a9e57113fbf90bec7c67cf2a771e1e4882e097910b26d78fde5b17d2a3333f31348681ae921db74c176df7df7b58090ff5f5a5a5aef8888885855ce5c55e084341706da4186b5a67607ba5f61ff879f7efae94afa24b21ba858d0bc76edda45e3b16e01a9fd1b58de40dd87826d24713d5ce7886de14a68d3a5170e950f860db66ddbd6003bfd1e69a59b18028680216008545d041c11abbae6570ecb3941d647ba7382adad932ba17b013f6437a881789a7672e2ff0922fc4b506553a85208e821a7a38e3aaaf9e6cd9bcf655e3c02b1ba9c79d2342b2bcbf583341706da41d81c51a3ac2367e86643ea56e2ad7c162fe8bfcf39e79cc9a485f4bdc883070f8e79fef9e7bbd2f60d78592f256c84b71913bcee56bfe6376dfa88b6a225493e6478139993e8eb43ddbb77ffa27dfbf6fbf43e5bad593ef1c4139bad5dbbf63448fd7fa8f701e4602e1e2264977e73e0e3ece437e5bf70a00ffe38f6b8b842ca78d1b3f5b602c3c410d82704acb0215079103072bb8f63c149336cf9f2e5f5b90dda19e292a0ea4873277ed8800e03491e4467cd01071cb074c284097a1558205dcbab42083007c21e7ffcf136f3e7cfff3b04ef5f98aed77db9f9c13ce1d0e32e805c24c08e7a1c51530889cb82642ea5fc0bf5ebd77f64ddba757343f9f53199317efcf8c8c993277780f00d813c9e4fd89079ece596bfb29dcdb4efe24176f2ceee806c7e8ccdcff4eddbf7fb6fbffd363d489980d97a23c9fdf7dfdff9ebafbf1e8c37f94eeabe1072da98425e8e1d4e1c7b14e78251eb7cc9dab509bfc2b22bd5934f5f36e2b5dd4819fb3a5a012816180286802150d51108abea1dd8dff6df79e79d31efbfff7e6348405da4c8ad4d484130f3f26262625676eedcd91e242b845475881e7ffcf1f5f45a2a08e240fad3061205ef0a771e43e609491e7701e422c5ec347724ca821c2ac8c33bb9028236910ba249d75c73cd7af24520951712a1ee7088637348745f2ed64ec2ce7a1048afdaa72d673be9ae2da5b948c93bb9a77fa0d3eff4e9d3e7d7a953a7eeb5c716bbbc83060daaf3cf7ffeb3e7575f7d7505365d4cb39d20b0d1d823cfeb1e0f70a253c4e32dfb25942bbce571e764794a4aca72086e48bddf851bb1b821600818028640c52260e4761ff18e8f8f4f8004b4e4049ca813aaaae3d89d587d7185124ef40a0a4b3644650504684de1448b576d04b8a51f3367ce9c6e10dafecc8bb6f42602f17b127df34469c59144df3cd13c920e7528d01adb8f487b17efea8ad1a3476b2dabd24326e79c734eed2953a61c8b17f3746c6c4d5bee628d39ea886de1862095850f9d47b71079cc111147e1835ebd7a7d479d7bfda0247d0f3fe38c335abcf5d65ba752ff95a9a9a9e7806b3b6c8bc246b754031de7b525dfd9e90b955e384e194cf26ffae8c506faf62d1798bfb66bd72e24af25f3d76e1143a0fc10b09a0d0143200802466e8300142c1b2f571227fa56e825c4c6c6bab57ba4b993ac4ebea4fbb7c2c73ae992910ab959bd75eb56ad4be4d0b6aa8e803ed0f0ecb3cf76a11fe7310f0e26d44704088adf983bee42082fa4236ad282bcb9f9a350848c7af2218b0bc87bffc1071f5c1aea0f3450af07121a337dfaf48377ecd8d11fb2d7157ba268575901c547c4d50f9149cac91bba890bb68f4f3bedb4f73efbecb34dccf5fc80959490497d5ebcbe2db16b10f55f0316a75157532442bf2549e1a2e83bdc0aa7a1eb013b47be7d782a0dbb93399ece6ff6d32bafbc72036921bf58286c87c50d0143c01030042a0e0123b7fb88359edbc4ba75eb1ec08935bef0ba444ef2ae66d25da89d2fce89548792356ddab45917aa97d9abc23289298714014858c4800103bafcf4d34f9753f16910a85a844137f49c5757f30302e7d717292b982b1b218b1f4036178c1c3972af6feffb2bde3312f6c30f3f34a78d7369ff58bca349b4e5955d7baa164d91cddcda77899062f5231d6fe857dce67fb5478f1e7f60ff5e9146700c3ff4d0439b7ef5d55783b16738178147625f2ddaf3ffcf2aad7d94739e5dfd264588a94b6b7f6751fed59b6fbe79c1638f3d561e983a4c6c6708180286802150f108f84f1415df74d56f915bc361fffef7bf93f0bc36a237319c340976ada5c4d3e4bc459c8cfd1e399759b0235ddeac15f5ead55b5b9064411546a05bb76e91975c7249a7b56bd75e0781ba80f16f4258aadf171e447fcf215cfeb84819796990b237218e6f33df76fa334318a95fbf7e3c2454b7fbf585af869055770782391ab415fae881cc3a3dd94b3d3fd18737ba77ef3e1f7bf786d87a5bb56a15f3f1c71fb75fba74e90df47f0484bb39a13ce0ce2eea77eda96d1709b0a3ac87fe38720b61d7ebd7b4b6f667ea7b8a0b8519d89812a0b865d57004acfb86802150351128d5c9b76a76adfcad6ed0a0415c5a5a5a734e9e899c44dd8be3d5aaefe4ab93a98e0b0b277fffad5348cbb2e4e4645b6f5b18a0aa198ff8f5d75fbb6cdebcf93e88deb9cc893a8c6d98c6ba34ddf19134e9438a55249ff27a7a7f2d73eb112e9a1e18356ad41a88d8de9045d51748f4a68193988797306fdbd256187d700f68f9ec0a58d8eb75bacc7fcdebadd8fdc629a79c3265c68c1922919ed2fe41a4c3b83888a7bf5dd6af5fffcf949494f7b813321c125a0b3bbc886b47f5f97e5794519b4a2a56942f5d95551ca57c8e37d1bfd7884f193b76ac3cb8446d33040c0143c010a84e0818b9dd87d184dcead54ecd3959c6eb04aa13bcaa531ca2e03ff17abdcee1a4acc25edc5c4ee46b8e3aeaa88d2ec376550e010893178f6d1c861f959191f110c4ae37c4362e3131d10b89f28f3ff9256e5eefaeb9a1b9437d4e0f82994b5c173dafd6aa55eb056e9daf2d2f625bb76edd4eccd7ebf00c77844c8641269d0dda79bdbb6c53bc2491dd0565f2bd5eef0fd8fb4e69df8c401fbd8f3efa68747c7c7c23ec387ad2a4493793f614725d74747407da8ce67744b55eb72e193b3dfa23df115d32741854a48f523ebfb71dd83b9dbb251f736c0f9001826d86802160085447048cdceec3a8e2658b838834a58a78c4799708dde63b11bb8342bb4227e4ad9cd4374d9830c14eb285f0a92a51085358a74e9deafef4d34f27c5c6c6fe17e2740cdec658d98f17d4115b747418507c3abe7901419677710d847152ebd6ad5fb9ebaebb569513b1f51c7ae8a1b5b830d36dff6e9072ffe7672196ee13bb3edb0275003b5d36f33d05223a55af287309017673e7ce8d7ce69967ea26252575b8eebaeb4ea6efa3c0ec7e88e730eae8919999599b8b05f70a32d52f3b7c21ba8ee82a4da2667ca1e23e299c866df990f7edf4f50b48f46b23468cb0af01fa80aa2ca1d961081802864008113072bb0f60426e63f1763586e0c6414a5c4d901cffc957693a19bb8cdd769cc4ff4077f36ec976580510d097c7b8f5de78f5ead5a742044742c67a22d1329d71756b3c45ae206b4a0a289a2352903ea1961dac25fe6ecb962d5f87f82d298f3723d08e07c2ac8f8f744d4d4dedcd3c8e2b3c4fe98bbbc350384d658a13e9d0cf4c70f8166ff597d4ab3eeca12a0fed6bafbd569f0b82f6bd7af53a06827909a4f30e7e3ba329378802dd219f0d21b5e1c2843afdef01d631f96e539c726e0dadda76890176e8686dfb56eafdac458b162fde76db6ddf60a3dec11ba0946519028680216008546504aa1bb9add0b1e0f66b1c27da7a9083684ed0ae6d4ed0ceeba5039fb7497109275a7752569cbc3f08b722b6552104f44684279f7cb2c59c39734e87305dc1c5cdd190d168c41142c6d5ff801573c3f54c44cd450aed44d274a879a37c747309d73047de6cd2a4c9ab37dc70c3a291e5f3660435eb99366d5a6ddaeacb41536c70eb0f38f6cf4ff547427ec00d9d7cec5e8efdef405897f99421905163c78ead7dfffdf7373fe9a4933adf74d34dc75f76d96583d6af5f7f1365fecd6fe66ae46f10e9c3c0b121364420aeb842ea7371ed74ac504219054ea8c7a38b0977c04e71a511750f7312e681f346d23ee62ec973575f7df5975c306c27dd3643c01030040c816a8c8091dbbd1c5c919cc9933fd4ab9e92a8c2bda49fb0c8c649d51dfb4ed43a168120313f2e2e6e2d44c84eb480515536dd4ebfe38e3b5afff0c30f67e2f1bc0c52d703dbb514c19143e27b6c1a6fdff82b93dbe38e78f9089b08193af22eae8588bd09097b69dcb8710bcb93d80e1d3a346ee5ca9587402a7b6193d60c13ecd526bbb7e2b59dc65d88ef209e3190f296175d745167c8ed31b7dc72cb5977df7df7b059b366dd0a56b7a37335b80d408ea3ffed99ff89b45aea07efd0dd63a30e97a6df962fae046cc905cf7584ef81f9b363c68cf97ad4a8513b9457b3c57a6f0818028640f547c0c8ed5e8e311eda68c8414388494072c309d6df02bafe3827f7b5943772eb47a47247743103b16d87c7f6c29d3b770e662cbb419c8a8cbd08d6eebd509ae68044798cb9df3bca058ebcfc7a33c246f25f27ff253c9d8b070e1c586eb7cdd58f1f7ffcb1031ee70b20831d69d3ff3f40b6725c962d2f3232f257eafa1d2c3abefcf2cb973efae8a3ff9a3469d24310d9fb21f53783d5dff9ad9c45fe3190daf6c4e5318e80ecaaeffeb6c0d31f2f4b04dc3c90575744f6638f3ce8c26f15bfb117391e7fcf3df7ccc66b6baffc7228d9ce1030040c81ea8f80ffc456d6aed6747d3c6e31f1f1718dc1419eaf123d773a89a3e36e9f52c6111b4ec6d9108b75dbb76fb713aec0a9e4a20f0a705bbdedc71f7f7c1524ed728898be401643e81f7711ab40dd80e8f9b37dba69696922871ba9733c73e3e9c71e7bacdcd6d8aa71daf542f29aae58b1a22f24bb0f36e9ce83eb03795229ab8451ee40e41ae6f35d999999d783c9f910d83ec81190cb8e78751bd3b77874c2c9f3b7c5b1486891f6c82f725c9a03daf0d017b7ce1da29b0f9619f46b11bfbb47283ffeb6db6e5b78fdf5d7db2bbf00c33643c01030046a0a02466ef772a439a94671326f4871911c82e2374ef2ee3634277fe75a3be7c20000100049444154a9c29394cfc9783dc7db38c1eb5da6c517b4d44a8140b76edd22274f9edc79debc797741d20641c05a306e91902847a888bb3723043296b9e22e6a187b5706f2a54fc2e672bc867a1e83003e8dd77365793d3ce6b3ad75ebd6b5b66cd97234f3b61fd28c76454e83daef2b5f4ca8579ed5039376cce7b610ca26ccf70448a696e9e80b675eda716b90c9777d0743574d6970738a25ec68d391638552a14d7d1d4d6f6cf80caff17fc1f855da583d7af4e832bd6f57758548ac1a43c01030040c81fd848091dbbd041e62a0ad3ec5dd53f284256e10187722e664eb9e00c773bb9e9371322766ad592cb19c65ec5f049a376f1efbf3cf3ff782983d0a29eb0771ab0b510d4b48d0eb8d4b6f1b65ddf86b1e40baf43e5811aee5d4fb2473e195175f7c71ddc08103cbf54247cb11525353bb2627275f8c0d8762bd2ece08f66d630eab022f3f062ff828ee883cc76eae33cfdd5d0b65d05f7781273c20c24ada430aeadb23bd7042611d7e53fa0de580ed0adafc1f6dfce7d65b6f9d86fe36f49447d43643c01030040c81ca8540f95a63e4762ff1e596721427e906148f414adc7c27714ec27e1d4ec41b20493bfd0916a954083056e1786c9ba4a4a40c8020ddcb381fcb78f9979f90ee1169834805b55be35f484f1f12c88c8d8d5dc81c784a6f4518356ad4eaf226b6f4c77beaa9a776c0e64be8cf51ccdd046c704b048276208802753a322b0f2d753a124f7b1e1dab28b8b97c30d4a113e1211d6c71c7bbef4a4a97de6e7979a425231fc7c5c5dd49de9343870efd096f6d1a69b619028680216008d450048cdceee5c0fff9e79f913b77266bcd6264a02af092b96c4ebceec4af9093bbde6fab93b2cbb35de54160c080015189898907fef2cb2f432081d742ce0e43749bdd19a9f19390e608ae4b0cb093aeb2f12aca83a9b59ff321b6cfb56ad5ea9d2bafbcb2bcbe3ca6269d4022bd0d1b366c141f1fdf7ff3e6cdfa825a1d6564646428d867a12f6ed94d6464a4273d3d5d7d7475fafa2d528b0dce63ab341d4b416912c58b13e916974e9abcb1f2726bbdfa6fe03abe76edda0f75eddaf543faf4c704fb280a10d9660818028640cd46c0c8ed5e8effd5575fade508ee419940276955af13ba447af274e1cdd327778ddc0a9c4a227acd57fffefd9b7ef4d1477db8f8180e79bd8cf010c4ad4bf599a93194e8181d05eea24511c65581471e4c17614779f61edd365f1f1515358db2ff3be08003265f7ae9a56bf030caf3a8fc7211ea0f83a8d74f4d4ded07f13c8f0b2dad170e5763d8a1609f4524543848142fe8afab77f763b559385f4a4a13315628d1ef43a17e2fca2f08f309f56aaf54f2d682f33cb07c8df06e749e3ef9e493677dfbedb77a67b4882f49b619028680216008142050230323b77b31ecfa42557e7e385edb7cbd0a2a2886900a3dece25ac2d3958fb7693d07466e01a1326c83070f8e39eeb8e38efcfcf3cf6f607c46734b7d505656563b6c734490b0c40d82e57f200be2e5f42092ee2b653a8094a52373217a0f33ee775e7bedb56ffdf6db6f7ad0a95c892db6781f7ffcf146f4e762e47ae460e661946caa4c22320bd6ce0b0e6175eb74659fc8725c5c9c3cbe7af04e0f5fce27ed0d88f03d60f9cfd34e3bedae7ffdeb5faf3356cb264d9aa4577fa998892160081802868021e0094acc0ca33d11e8d2a54b7858585e2daf37ac4c64414488daf239996fc6ab9649dcb6fd8c40af5ebd22b858e904211dce6dedbf43028f8018d6868886edee650c666ac1f83aa2c618eb167d0a84f20bbca7f75e7ffdf54f6ddfbe7dfe830f3e981aac9e50e4b76eddbad68e1d3b4e81fc8da04f9de84b14fddab7aa435c1a92ea2efa447021aeeead0a5c08b85620b19eb4b4b46cc66129764fe078749f3e7dfe03617f1add2f274f9eac0b0423b50e2ddb190286802160081446c0c86d61344a19dfb123098f5e5882d79bafb598411fcce1e4ec5e83a493394d644234d2eeb8e38e72f5dcd18e6d411018316244f49c3973f4a5ae2b2051a7a29e0841f5c6c4c4f83d88a405dc20af1e8dab4899e2522e08d3083f26fde12143867c3a66cc980af3d443d86320d28731ef86624f4bc47d054c764a38de2f9bda96f81ac73eb75e17b2aa0b01775100665a5aa0651c9b19934fc8bb1379fca0830efaf8b3cf3efb63d8b061d9bef2161a02868021509910305b2a0f02466ef7622ca2a236782141b15e6f1824377005be93b94ee412b4759bda4ed000b13fb7fefdfb274e9830e154bc83ff0c0f0f3f0fef663d2e3abc902bf7609442dd160f6623f3c02d4ba0ac0be585a46c2e651790f722a4f6dbb163c7ea41b2605585241f2f74f8ead5abbbe0851e92939373387dc38c308f6f1efa1ad17128c4575f4921edfb2fec7ced61905f1ddc456af3c12c1f7b77c6c7c7ff48e6f3e8dc8edcdda44993f7f3f3f3d7ce9b37cf7e3300639b216008180286407004c282ab98c6ee08c4c6c6ea21a3684ed641f143c75f9c13b84886884ece8c1933fce916a958048e3aeaa8e6df7cf3cd1048d328bc837f236cc0d8380f3cb7bfdde75c45ca20be410da39cc6d4791ea50c4113095b02b97cf35ffffad79cebf7eaeb58aaa9ec423fbcb4d77addba756762d749515151d1a4b98a14fac437277da15328a79daf4d85d8e491284e73f2d0ea41b134ec58c845c1abc83d595959ff898a8a7aa46eddbaaf4d9932e5fb356bd6a4932f5d8ad866081802868021600804472028390b5e45cdd380f878f12ae96d0941f1d3899c93b3f35e09298e33f0f2e572ebd84ed802a40265c08001e1f5ebd73f7cf1e2c557a7a4a48c80881e89c421eeb6bd882d63e33ee7aa90310e6a1d73c1796ca58f72664c4cccfcc4c4c4e721711f76eedc790b6915b6b56cd9b23684fc786c391d92d880d0cb7c73f629ac30430a35040eee88df803e5e21c901d7cd10d8395c24be4efabde8dcc5f1431c3ffbe0830f7e9c9999f9ebe6cd9b937bf7eead8f5db8f2b633040c0143c08f80450c81200804256741cad7c86c080da4c1131516e60d8a9f48858413b86ebf0aaf1c3c547a4fa7e2261580c0e8d1a3f54aac7a1f7ef8e159a9a9a9a3d2d3d38731266d19930808a05bf7c9b15b670bf1729e589905f15210500acae733a65a53fb39246d5cbd7af55ea1cd15e5fd7186c2863dfcf0c3b11b366c38068ff1006ef51f485ea46c23749bfae21397c04e7d2608e5a60b36cd6d91523dec95499b3bc17425b87cc3c5c39b88de1af1ef8484847fd7a953e7bf6035163bdf665c7e4a4e4ede3c72e44895b1f5e8a11c15abcb1030040c811a8640507256c3f028557777ec08e316767e783e7ea8600538b93b158578083d9ce8b5b65024c0a5dbae7c118064c6dc7befbdedf1645e4c4bd74168cf80d4d5e158de77923c8ed46a7c24102d97a638baee82047dffb2032e6cfce497b1946e1ee3ba039d29786c9fbafefaebdf5fb56ad57adaad3082465b11b7df3efaa0d8d8f873d3d2328ecccbf3c4ca36d98a5dce73ebf168ca1527ea4260511dd228a88f59efcd214d2434039cb4ac40ef9f4da6cd8dd1d1d1bfe0bdfe0a123b99e317387e147990b2633a74e870efa1871e3a06eff2f8ad5bb77eb276edda5f21e47a7b448561a57e9818028680216008546f048cdceec3f8e63bc210b802488023483ea244a8a7f121c781cb59eebe2100e1d3dcaec36deea3b8cd7d25e4ea1a3cb63d20a2311a13d50e3153e027abee60b71d5e5097a2327867f5de55471615672c73a8771379efe0817cec861b6ef89c762bf4b3cab4ed1d376e5cd3bcbc9c33f0da9e08a9ac8d4d6e7ef988baebc05eee8491fa4a3b1eb0532d39b4f13b047626fdff187987e3979109b4fb08f20098dd5fbf7efd7bfbf6edfbdf33ce38e39ed34f3f7d3c84f6839f7ffe79c19c3973b454c3c8ac903431042a3d0266a0215035111001a89a96ef47ab6bd5cacbcff378733cdee0ec56c4a080143872005930cf6d398f9dde1800c96c0e013b232525e5bad8d8d8c1902bf751069134c6c02311f95318cc9cf8f878f720946f1c0bc6348b5bed2b29ff12e1d8134e3861366da605ab2bd4f977de79676256566e2f8fc77b16f635c9cece0ccfcdcd76fd939d1eff9ff8aec49f50aa08e4d5d5256508ac8274d2bec62b3b2e2e2eeeeea4a4a4ff1c7becb1a32fb9e4923bcf3cf3cc075253535fc28e697ffef9e74f1f7ef8e19f93264d4a477255d0c41030040c0143c010a808048cdcee05ca90226ecd7a72f2c5718394e744efc80124c8c3ed5979fdbc4a0b52acda65575487446c2fbae8a243131212aec093793db8f7c3735b4fed0b7f8899f3be42ce94542a81b0b965099065a70f411689fd89baf57181c76ebdf5d6c5fb83c0e93dbd77dd75ffc11e4ffe051919e91d209f91d8e4e61b73d45d4c3983f771a7ba5485308330e7719c8cfcca05c3bc9d3b772efffcf3cf373cf7dc73c98531c00ead8150311343c01030040c0143a042113072bb17707362cfc795a72f8ce595a6386448a496224e5d6f59d0c71f4a53d474ca80c02db7dc526fe8d0a1fd199f9bf0d80e83ec1d0cf69122b49032b73c44d541bcb4f659d1a0c26d76f7295dead45b1444d8b651df74ea7e12d2fc727a7afa1a3cb66e608356164205bdf961fcf8f16da2a2c22fcec9c93dc6ebcd8f4e4f4f75f38c3ebb96d44f1729b293f756b22b51ba814475285fdaf45741147d6f01d16dc481f09010b5cd103004f601012b6a0818022144c0c8ed5e8009a1c9cbf3e6a771d20f4a6a2001ae0591abecec6c7900e3f1204692f817c3e0c0b6bd47007219d6b265cbc3c68e1d3b0c8c6fc6c37a3ab83782848671ebdc91da9c9c1c8f3cb06a05fcfd0f91e9389030c6eeb3b0e8e4c5c7c7afa5de7790a7860c19f211791b49df2fe40e6f6913e6d4d978a7cfc8cccc48c4164cd9b589902a56384dc77b23aa43f589e417948f04bfe690fd46e4d91c2e00c50243c01030040c81ca838091dbbd188b5ab56ae57af3bc2914cd41026e102ce7258408383d48413cf1e872fb88836ba566ecb404a157af5ecd5f7ffdf573d7ae5d7b3dd8fe1f04f6306e972708770971ffed79c8a023ba999972ba7b74a1111028c6c9e76dcf898a8a5ac645cd2b10db67870f1f3e13afe91648df7e21b6175d74511264fd4488edf97979398de3e363bdea08b62970de5b6c73cb135c42b13b1591149b59245138a83e2512d7ebd31ad27ea3091326d81d0881626208180286802150a9103072bb17c3b163c78e3c4ef63b3d5e4f50722b42259205f1722d512e262b2b2be1eaabaf36ec1d2265df41b0bceddab54bbae9a69b8e9a3367ce152b56acb889b433f12e36a3b6283026f078140a7f851efe340e9032621eb7d4c0177709bbeda8cfa550561f66f811dd6729fffc55575df5c3638f3db693f4fd426c070c1810f5c9279f1c9a9c9c7c3e3674a2cfe1107a672bf6b93eebc067bfe2fb22d4efc832fd771769cc632f1707f178c75b6043ad7da9dbca1a02fb03016bd3103004aa3f0246b0f6628cbb75eb969b999995ecc9f36453bc4492234f9a48015e3fd4766d900592c3ea2d5ebc3876578aedcb8200a4cd9b94945477cd9a35676ddab4e97a88d65510ae6ee09c8027d3cd67745c950ac9737188a02369be900b0c97ce6038d2265dc5154ac8cc87c025231f6767678f3bf7dc739f257d29c47697db17858ade20b6e193274f3e10327b7e6464e409f4adc8871a640f362a70e28b33e75cdf754c5f2b7e1d00001000494441547f7cf17cb0d0dc9538fde276b4e10833bace8bad108941b7e35d77ddd582d03643c01030040c0143a05221e0c840a5b2a80a18c3c93d2f322e2a154f593a713f3920ee8880af0b22138a8b48c983289201091399928731497926a54740cb10c0b83184f6544add48a80f3268eda7e671917bece8a152fca63c9f88bc496263631d79a3447e7c7cbcdee5ba0ab2fc3a7a7791ffeaa44993b612f78f357a15be71c7a029f3e82ce6d599cca978d91ccc08117689f428eb494f4fd7fcd33c4d277d237d0aba6e9cf654dc89e2e011cd3cee00feadb4ded965d8ce1030040c0143c010a824088814541253aa9619de9cb0cce898a81d10846c0882c842b11d10a1550664c0479ef4b95779bc6a2bdda4d408440c1932e46088e7855c548c84781e04d18a22ee3c91856bd178143e0e14d70367d217e92314794d8738fe00817b062fe7fd696969730395afa8bcbe7dfb6a9df6097891ff86ad7a538123aac1da173e94716b8d152fd0cf898b4b58161919fd05c7fad21841f00d7c9c12b887834f23e6744b12e4c525b0cd10d80d013b34040c0143603f2160e4762f81f7c684654116b6523c0bd98360294d828e23be22b99002258994b4484848a8eb0e6c572202e0e59d3e7d7a44d7ae5d9b4747479fc2edf85b2076a320554780a757d8162e2cf225299c56384e7d6e9c14fad221af6e7cf06a6a1df5a6a8a8a86990c1872193ff1b356ad42a9fde7e0e8583bcfda7605b17fa1e01f12e934960e67b802e0f2cd7e0757d2f3737ef43f0d2e76f4b5517ba0eab82309e429dc68e1ddb183c8b78cd49b7cd1030040c0143c010d86f08540572bbdfc009d4f0d9fdfaa5c544c7fc8e4e2a277702cf1ec44989ca93e0e1d5a1088688405bc846cb471f7d54efbc75e9b6fb0b01e13260c0806678558fecd3a7cfb9bffcf28b96203c1c1919791684b601e42afc2fedd2c534068535752c218d203f99f1f88e7a9fc26b7bdf29a79cf2e1471f7db48d5bee416fd953bedc37ec89c3537a02b6f5008318e27ae7ae5b5e10ac71f49d0a84586f8dc88e89895b959393f76e5898e7cde8e888b9cccb6df45b1e6ba71768075085b335778f4c49493968d2a4497ab55de13c8b1b0286802160081802fb0d0123b77b09fd39e70cdbe909f7fe0c31d84615c5920391013c82647b3c78dcfc5e2f12ea89a8dc75d75dcd89db5680c075d75d177bc925971c74fbedb79ff3eebbefeaeb627783e13d60fc7770ec00018d2a50755f19f3c5f726a44e3d509505b95b8927f36dea1f7dc209273c3a7efcf81f3ff9e493527b33f7a6edd296a1efdefefdfb27624f6fecbc98722d984761f1f1729a7afccb5c482f7103339707b9d5f29925191919cf4747473ec3fc5b9e9c9cbc9acc45609183102d79c316ffc51b17186a3b82b03de54ec4be66e4dbff9292e12be71cabde1030040c0143a0300276422a8c4699e29bd293b7a7fccec95d9eaf5c5f518e7d511772d277a1d2152f201be178257b729bbd9dcbace13b3ca411975f7e79c7679f7df6ac8913278e84c8dd0a4e97a7a5a5f58640b58688c573ec157e828a7c05a5f25c3ac5423b8d83ca2329883c972f1e72c8218fbff6da6b333efffcf32dc3860dd31b300a95d87f51886dec9429530e4b4c4c1c9c9999d91d7be52df57f8c02c21ad4386116131393979595b3363e3ef183a8a8b877060d1ab48c82eaa7d6db4e279e8c9469635c4470e3084f78f9e597bb2c5ebcd8de795b26044dd91030040c0143a0bc1028426ecbab91ea58efc0810373a3a2a2b74238d6231925f551b79045424432a4a3b8426ee7b6851874d1fb5a755c1305521b75e69967b678f4d1474f82205d058915a93d6ffbf6ed7a7f6b6d70d5f203f8a857cb393cfa137e3e4cc14f4941c5873d15c97b2e8fed7ae29f53d793871d76d8f3cf3df7dc42c6d3ad9d0e5a59052968adf1b469d35a4746469e8387f578c278faeb05133d90e8ace0d885817678a4f3f1d6263357676664a4bd3f68d0792b264c982062ab62bae3f03511ad1d272879032f615744d00ec386f678930f1d3972641d70d6921b926d33040c0143c0103004f61f02466ef701fb7beeb9536f4bf88d13ff4eaa115170de440805871e3f111019f314fc298ebef4f45ed69eb56bd73eb020abc60422b5c71d775c93c71f7fbc2704eeef10fd7b20498321619dc0a70e40e8697c771b9cb80b4957d4bf1cc1772c2c5d063bc895d31501549ca4221b04310b42bb9cf19944c6835c584c9e3367ce9a2e5dba542a628beddee1c3876b6df149e9e9e9a7616b3d08a4238ee0e496b890e6faaafeabbf3af6898ee9a73b245f771516e7e6664d84c02f7ee185170a5f88e553ef92bc3ccfaac8c868300863ceea7a228cf919e1c455c20e9b5c7bbb8764c5d2c6315f7ef96507e2614875deac6f868021600818025500013b19edc320d5ab576f07446909b7ccddbadbe8e86847be380e586b01418013788f5eba74e99143870e8d0b58a09a64ca1bd9a14387fa780e8ffceaabaffe0fe2f600e4ea1fe07508a44d5fbb0a2f6b5785a5af8cf0673cdc21e03a32a60348ad825408f15cda79805bfc6a7736b7d253d073172552a82cd2b871e3b8b56bd71e8d3de7206d91127fa7eabf447d47cf79b8e9a39b8751515122af1b1312125e3fedb4d3be79f5d557751126b5c292161b1b3b3b37374feff1f5632605d5a3308884816177c8f4a1cd9a3573cb2682e857d66c6fb76edd2291b88e1d3b26b66ad5aa36173f0d081b376fdebc59ebd6ad5bf2d7ba6ddbb6eddab469d3817827939621c700bc0f346915720c98bf1daba2346ddab423ff573a5456e17f43bbbd91162d5ab4653c5ad2bffabd7af58aa8acff14cb6e57e52951e249b3f29858792df9fdf7df7742ce96c5c7c76fc6ca3c481381078fd72e58453a242eb1f85d83b4b4b4d3962f5fde033d6ff12a553f154f6d58ad5ab5ea9c72ca2987ad5cb9f2968d1b373e0f19d26bbd8e84702641a2c2e9ff1e1d2d2e6d0f25122067ec3d1ee1af32d4e9481a842f9f7632395e8b37f3c59898187dcdec79945743c8e4d1245ab936ec0f83f477d9b973e705f4e718ac0bf88f8f7eb8bea2eb962bd0578a783c10dafcacacac64faff319ef1e7f5f60797b1e7ce1b1e1e392d3737672d17068ee8137a28b7a7663129d2e3e2a4766e6e6e9f2d5bb654ea0724c1d63b7efcf848ba91c4054483b8b8b8a6cccb36fc7ebb82d7f14b962c399b0b9ea1ebd7af1fb579f3e6fbfef8e38fa7d6ac59f302c71357af5efd01f1e9ccdf6f9179e42d4016f33fc0e4f7df4385c1a255ab56cd477e02d74a21b2259054163b4bb283393a1ff991fcef9139bb0b7d9bb39fe57bda2f51366cd8f03dbfbfb92114d5e79775ebd67d1f44e6905f580aeb7f47de5705f235616171e9fccff86a3799c9f14cfe9f7cc9ff910fb76ddb366ec68c19fd3987353392cb7fe6106ebb5858082bac4955dd71c71df98d1a35da0489f8997e6f471c291039503c9870b28d801cf4983973e669679d7556a52606c1fa525c3efdd3fc4a7cfcf1c70f87b0dd425f9fe362603844a83d1845426a8b2b56a634ead47b83fd1714d4cdad75afbe32960de15b07f1fa08527b3557d7b7a7a6a66a9c72cad440052be3a1680e2e8320e627d17440624bbebfdf8aabefc203229f0fa14dc3733b13b983bc406f7fc84f4ece5ec23fd7e5609582be5e19e6619c28167c539b78c6c318ebe390cec14b944e635fb4b0c3bb68d1a2a83e7dfad4e2f7d990f13fa0418306ed21b247dc72cb2d673669d2e466c8eb93e0fc667272f234e6e60c2e32a780d90bcc997bb9b0b881f00afa7616170fa720f2a21f4cd812a98f24206e6c0835df4cbcde5060e0e52f0a8936f18604037e4751883eb49248a8af621611704edacf9248fb250abfc1c4104b12f5f985ff0172ae04925ae81496c2babaa86f4c7d9246848545698df97fbcbb34214dd28cf020fecf9ccfb9f069c2bbf81fd493bb9b1a2b86cab67d4540e4635feba8b1e5f951e60f1b366c2327cd9f886f16a960827b20bb8e74705c22369c8095e76552d78654f4fbe4934fceb9f0c20beb2bb1aacbc48913a38e3cf2c8c678c40e059b41108847c0e4fff827711098b81f2ffd76dd0437775274077bb1a34e8735ff207ca5e57ddc0a919d03a979825bc77777efdefd333c16baf8a814efadf519ba7bc8edee5a5cc90f84649dce3caaa33ef9f0292944cfe1171919a9b717a8ca7c30c9a0ec7ce47f7ffbdbdfd691284c088adf7af53a2299f25f878787ad639cf219238f48aec2e24bfc952a3d1161ec4802ef13b98888fd2bb7fc63fc8ebcba33f0c1071fc411364e4c4cecc82dbf237bf4e8d16fc182055782c168bcb24f81e92470797bc78e1de3991bffc0b233c3c3c38f21bf1dd8d6211e87fdb1e8c490164d9f22480f23f4228ef0f33b75cb3dd07158d3b6f39a5b981f721c7c18efef30d8d8ee6ffbaa7bfbfc06ddffb7f20af53f2e90046b375059e5f17fd1fdcf2829e47f4d38ff57f49c497ffe5f0defdbb7efd1fc1fd30509ffa2026e9619040123b741000a96ddb367cf1df5ebd75fc43fc115e8664212f434be3bf9e91f0f69256e94515e3827e436e80e78f7dd77cf10c125ddab8caa26975c72497c525252bb9123479e84e76c587474f43d908a5b200747411c12214e0e175fbff861bb28fd75e1ee3bfd63d93d6df7639595a86efe99c85bb98c70227a63205a2f3df0c0033f73dba752aeadc546ffd6ab57af186e57f5812c9e8317b515983942e5572821e2c348fa94d53fd25cd27e43fded8b2fbef89b499326055d7e81b7202b3b3bf3fbf8f8849594d597f7dc051a73926a026f05b8eb02239c8b9863b98d7810252ae4ffcaf8f1e36b4164db8d1b37eee82143869cfdf4d34f5f0349bd7be3c68d4f30271e82c4deb469d3a64b084f069b43f08cb460ced5c5331bc71c8924dd2bfbb1d779aa333232dcd20e61a9344e3ae57a62056babdfeb350c0c8312e700bfe3905f3815ae53ffe3024961dde2e281ca2a4fff470249c1ff1f2fffb76ae3f0393e3737f78c3973e6b40a54c6f24a8740859c844a674ad5d4eaddbb77ceb9e79ebb06523787c9bcdd7762f47abdee4419ac574c6a2fa420861369577e3c974070fb5f76d9654d895789b1e12a33e6fefbef6f0e913afcadb7deea0fb9bf7eebd6adfff27abd7fa75fbd080f0097087eb80e0a08affb67a503faec8feb78770183dd93f638a63dd5918797783dc46526ed3d51af5ebda7fef9cf7f4e5fb66cd99f03070eccdaa350254bc0d31d0e668732172ec4b3d8155cdc2d6f8e4b6569219ce499de00719b41d98f207fc53d4056b44e8ec02cefa28b2e5ac9dcfd3e22227c23e5b556598495dce09bdaa73d9da0da310603dab56bd79a7911f2f94b9d110f3ffc70dd7beeb9e7c06eddba1d7be38d370e605e8d8c8888f82f84f57648eddfc1ae2ffdd032983690d786d896c07104b86a9eb875d99a83052715d739745c483d8ee482873b569f9467127acfac616a9896660ee8b75895c5fd2309b053dff87fabff3b5ada559f73e5d153a64c3958e78400c52cab140884fc04548a36ab9d0a046b0b5ea1d9782757d1b95cfd68093561150414aed44482bd84099c708f4479e89b6fbe79017f1d478c1851299f3e17c9c0b60610ca83c78c1973f2bdf7de3b040fe1edf4fb1608ed40fa7024c4426b88a3f9b17a3916f171eb913333335da834910785fb20791092edfc835808a9798370dc7ffffbdf89c3076a47750000100049444154870f5f8c5dc91c07bc1dbf0fed86ac289879b93868b664c992f3e8cbb1d81c071973cb02981341dba1bc23a19493b7556456f3f0c35b6eb9451f182975ff6fbef9e6edb9b9f9dfe4e5e52fe59f6db6dacecb13570e6c82c6503680bfc65844b23f1ee84bde78e38d7678841d490f5c43e0dc010306845f7ae9a5f5b8bb71e0238f3c721ce33b90f976c3e2c58befc6be1b21b5e72347d3fe81d8dc283d3d3d0e7bc264976a26bd88075ce99a83caa39ffaedb9bb0960efd7a3bcc394faa46662081802fb0901fd16f7b7ec4bfbc16053ddfc1f73ff6ff81faee75074deecf0e79f7f6a8d74b0e2961f000123b701c0296dd61d77dc917eecb1c72ee164388b32a99c2899b3f93ad97318786342fb48b09742f19c780fa59e61efbdf7dedfb9cddaf3ecb3cfae17b8868ac91d3a7468a448c639e79cd3068f60cfe79f7f5e24f6167e987740ec8761f3a9d87e1004bd1ee2488d8805f9ce4085e8b878e15064c32516da099342872eea4b132121219fdbcce978e05681d934d21e20ed0948cf97786cd743be2bf54363d8ea366cf7b66ddb3669f9f2e5a7814f5ff0abaf346572ece60fc73a747161b0bb2893f926af642658fe8cbc7fdb6db7cd05830ce59556f0b66679bd91bf7281369771db4c3df985cb166eb770baecd4b1ec44bc946d433f06ad5bb7eef2c183071f0c39ad852d65fe3fc3c553b496e87cf2c92787bdf3ce3be751f775d47b2773ed868c8c8cf3986347137620ad0112253b24beb945becc122e2ed48e3ab46c435127d2d785840eb05b815f94e73fb0882160081802e58880fe37711ed3477af44c4ae3c8c848bd1ab31c5bacfe5597f9a453fd21297b0f39f1e777e8d0612384600aa5d730491d31d084e5b82c9b086e3427e8b69c740723b7cf9c39f3c29e3d7bb6bbeebaeb62cb52512874b9f51bd9ab57afda071d74500bc86c373cca177efae9a7776ed8b0611c64e016bcb3fdb1f150e2cdb0591e33e7a5f5b50d2ebe688921e51c71e3c7ecae5ea5b83b6e90584752c0d7e940c052202f5f43ea1e8384dc7ddf7df7bd07d1f9edfaebaf4f57f9ca297b5a75e79d77c66edfbebd079ec701e0d8062c8afc1e85830f43c525f4d7791a7da16a058b3ce6dc6a7099421d336eb9e516bd775959a516dac91f35eafe8d191959dfc4c6c62da59e1cd2fce5d5b64ffc89447c3abe90a408f45ad3a7c19b366dfad7e4c993fff6eebbefb63beaa8a39220bae1e4efb1a1ef252faa6bd7aef1679c7146fd030e38a00df3ad2717789732c7ee85bcde8647f67cc29ef455bf8ddad8178114996f7b546c098680216008546204f8bfedd1b98fff81be0bef30ce7731fcffac94776d2b31947b9856e464ba47ae25941a81091326a41d7ffcf10b21181f72024ed5a4857c95ba7c614526ba3e6b5a9793f7f15bb66cf9f782050b1e7deeb9e72eeadcb9f3014814f9e5765287648443a663211875162d5a74ececd9b3af5bbe7cf9786c791572f19fe4e4e4f320568740689b62733ce9e11c3bf2c9719937912209f539d2a60a742cec446615d2ae92f5e3d79b00d221b25fc7c7c7df04f179e6d65b6f5d74d34d37a552c65d5038c52ab0d32dfb175f7cb105780e643c7b60bffe99f9c79534d70bd25da863893b28b4133e4832495f80c764bca46b29137c3d010576dfeeb863687a6e6ee6c2f4f4b4eff807bbb5b8f6762fa3b12f9c46dbea83086e43bcacfd981f637ef9e59771cce16153a74e3dbe157fcce1041159091750b5f01ab7fde0830f8e5bb56ad5602e9eeefee38f3f5e4c4949799939f12fe404887f73ead1eb7854afea2fdca4c50d0143a03a205003fba0ff9ffa3fcbff4d0fffc7758e23292f0dfe50a63b6f3510baa05d36721b14a2d22b406ab742bc9e61a22e6172e67162765ec940356862ef9e4f79578e502f9caf9f9e9e7eca8e1d3b1e5ebc78f1d43ffffc735c9d3a75fa376ddaf480468d1ac543404576f51184529df4694fde612d5e0f1fbfeb85f631aa272929a96efdfaf53b4e9b366dc49c39733e83602ce68af27d48c528c8e5a9d8d29a1f5f1d42dd3609a36fae3d8e7db6fa435f7f68cb172d310427476aa5abb8ea539cfaf54397e4d3aede40a107c3fe44e7a1a8a8a87f42681752690a646eaf881c65f7db86cd61e79e7b6e93952b570ea2af7de9733cfdf2f25fad589bd071e9e839cfb5c202c907a774f0984ed917f176aea0eebd5e92419df98f3efae8bac4c4da93333333e6d168a9eb928dd8e02e72e88becd4fcd05d8826fc2efa3087ff8dbcb57af5ead990d8050b172efc0ed23b67debc793faf58b16236f36c527272f2bdf46730fd399ab9d70c525b1b9b22116d98537423b168821d190286802150c510e07f9efbbf4928074d06ff0bffe01cbfb98a75a3d2996be43684433263c60c91014dcc314c54dd2676c4abb42761e94964924209712f1ecc70085e2227fc4e78b406e3a17b7addba755321036f70bbf75648e9df1212120e41b7796c6c6c337d79a9409a28ac57af5e33e5e1553e801fcda1d4a357230dbaf1c61bffd1a0418307b905f27a5a5ada2710e869fcb0fe0351e9497b8da95f0f08893ccb93acf594ee4708f91079710f86c9c6c2423b8ee42a9414ce2b2e4e7b5273f52942db0a5cdd780ff51aab14129660cf0464609b366dc60d1e3c780924ce614bdeee5ba53f7ee8a187ea42dc4ea37f67d07f3dd1af070a1db6325e38292c498411a27f84398cf782cccccc17c063c1902143f6f96a7fe4c89199f1f151cb2323a33ec23ebd1a2c2f983dca97f8ec15c995e898b92b921b01e14da0afba1bd188b9d69af42ee874c6fee6f4a51e79750813f9dd444186ddfb6529a32a1c2ee4b978e15d716985f32d6e0818028640654780f3b1bcb67abfb8de4ffe6beddab5170f1d3ab44a2db1ab8c181bb90dfda8646dddbaf50b4edaef41603650bd2361854ffea415bbe964ed134efccea3a972d4e37b925b24489ed306fc203a43084e85285c0de17d0842f012a4f36d08c99bb4fd1a44f045c8c10ba4bd88ceab90894934facef6eddb5fa4ce71786aff8b27edc66ddbb60da6ae7e94eb463b2d494bc406b583bac7914ecaeac7e78eb543cfd946fb2e94ad94712444f912da501054a4a7fa555ef55040afa1cae1783ba4ed57ec7f13c2734bcf9e3d1f3efbecb3e72d5dba74f3840913b2d1ab921bdef238703b92f13a0b1c3bd1cf7061e013c6cc5d1c146051a48fd241df97266ff67ac6eb83c68d1b7f7dc71d7704fa0a99af4ca9c23befbc737b8306f5be00ffe9b4b913515ba52abbbb127d756bcad41fea717d63beb9f9ac34ee74b8228aabefeea060a7b442fd2d48fd2b507d7f1d59cc1030044a878069551604f43f8ff380fe1ff2ef2e6f35e7ee4faeb8e28af9fc6f93f3a2b2985925ed30725b3ec3b61552f60aa46d06d56f451cc1252cf3461dae0c93dd114985fa31408e3c846ed902e4af2d24e2608ebbe36d3d1a4fec711093defc5a4e84d8f686401ccf8f48eb3abb515e7a1db76cd9d292720d29178f7e38de60e72da58c23b46a1482ecda44476db97cd923c2a1903e4acd89d25ca4d08eb60a1d951c559bd2c5467d80406b3d174180de8a8f8fbfbf7bf7ee8f5d7ef9e59fcf9c3973e5a44993b434a1e48a2a798ede5d386ad4a876fc03eb8fa9dde9770c7d7678abff121d0b4b093a7b6cbe74f47682ff7488edbb575e79e536ca86ec9fe1b061c3b2cf3ffffcdfa3a363dfa11dfda3cd52fdc81ef628413649945f5828ab6c377714918e42e6a902274a63ac1d06e0e1d234b724eea0989daf8d62b2429ee46baba430e40d5a85868021506310d0ff3cfeb764e05cd247a0dee32eecd4238f3c72538d01a01c3b6ae4b67cc0cdc7cbf83393f675aaff0629d3a75f29e73c5c22071050472a4502443215eac42fd10f43a27485b4a335aafa5c6818e4371c621b0191906750691ebc7ccebbaa7aa52bf2aa50a236550f84c9e9a81db5ad74910fe9285fa2b884361c29517d12a59555543f6de562ff56faa42fbdbd03f97ba45bb76e4f5c73cd35ef436a7f7eeeb9e7f4c05459abae74faf4a73e171dfdf19ef701bbbac28c7e6bccdc45842f0e1ece761fee3a104ebe90781a7973c0e94dbcaccb468f1ebdd7174faab33879f8e18733dab76ffd136dbd832c43b4e4660f55c6cc9fe6b35ba14f28e7cfc76637b70aa729ce1c75fd97a2e69fe698b0d0b1f2258afbc457b7425fdabe87be7f852ecce79e8530a5cf61ba4b90c1b13ce33b09f55be682354c6be27412da48dbba435393447d36f178428981e6d316e69289c7134a0cf8ad7a42217a034d28a4b02dfae08e5e67398bff65cfb46eddfab5279e7862c9c08103b51c8fa960dbbe20e0fe93ef4b0556b678045e78e1858cabaebaea2b4ed6cf3071274364fe40339b2b3482c09b088388a74ef03ab1fb42c5253a96285e5854abefd8175748fb8e2cfbf27cc72211ca57ba2f14d1f0e5fbd27d2457c73e910e7d72a44471a52b94edbe50752aae3c5f5c048772faf84226791b911fc168725c5cdce3f4e93f071f7cf06377dd75d77bdf7cf3cd82fbefbf7f0765453254bc4a0b0434828b8db3e8ef79e0de923e6b1d739171a1afae8f0a25c25d09e8fa4921e593c1783624f9e90e1d3a7c2d2fab74422db49f3f62c488adeddbb77f0f3b26306e3fd1461ae236df3ca62feee28b7cd717ec73f9da292e515ca2b9a5d097a650a2349f40fa5dd497ae105b5cddca60ee28d843f47b299c58b84c419ede8dac3b03d9e4696d1b44d5b3292c2cf2f7b0b0885fc3c2227f0c0f8ffc26dc1bf9597878c48791e1516f454646bd111911fd321eec17a222629e090f8f7c2a323cfa098e1f8d8e8c1de7f5863fc2d88c451e2ead80db4381a4b4f5f8f42222221e461eda4d94e6c4a717e2507d56dff7592222a21ed9bf1241fb41652cf8ee8d94a24c143a0e8371e0f068d9258232268c4f49383c46de137b2bfc6e9e2890c709f7467ce515aabccf96c7b1e961fe97ffc7e3f1dc9d9494f40a4e9c45fdfaf5cbe4d8b6102060e43604209654c5238f3cb27df8f0e19f2724243cc8096d0c13f92748490e7157242646cb673d1eafd7eb21cf89d7ebd5fa1b772bd7ebf57a2adb9fc8860422ea9178bdbb6cf4910e889047fd53283d7ec04e4f217dc983bca4a2fb0b655f4c4c4c1c45bfaf23fd367edc8f3ff9e493ef2f58b0e0e7ebaebb6ebbd7ebad16a496be89987a990b5df09c0fe2b81312c93c2008be8193be3ce6e608f1ac9898986fc0f5a1468d1a4dfdeebbef44d08257b2971a22ce8b172ffea371e3c66fd0e6e8c8c8c8e9788b9d175df6178ca9f33c8bb8a2e35a627c9d479f3174c7fbbaa3dfc2d055a3b8af7e97c04ec79a6f443d5c2479b051fa79d893c309290b022eafeb6af2e760f3dbe4df87fe48f28679bd61c3c3c2224684857947125e1f161171537878cca8b088fcdbc2c3c3fe9d959df71faf37ffeeccecfcfba3a3231f7ef0e1fbc68d1fffbfc75f7ae5b9275f7ef9c5ff8d1f3ffea9f1e3c74f28ad3cf5d4534f4b5e7ef9e50985456992d2d6e3d37bfae9a7273cbde79fd29cf8f4421c3ef5fcf3cf87445e7cf1f9a7f6afbc48fb4165fc8b2fbe584ef23cf53effd46bafbdf2e4b3cf3efd44d9e559ca983cfb6cf118bcf4d24b8f238feeade0a47a741f651ce57de2ea2ab0e5317eb613f87b9dff53d3376fdebcb677efdedc29e2bf946d2141c0c86d48602cb992b163c7a6df78e38dbfa6a5a5bdc989f54508c21a88817b402723074fa2d000000fcf4944415483bb9d054575d2961426043a69bbec4ab4937dbb4b7878b823e3b297fe79e89fb358e9900891f6fcececec1448c5b790b307f831df80de7d37dc70c3ebe47f09e1fd75fdfaf59b44a6a89b5bc2ae78b5d9d12711c023b9c8e900c18d621eb80b80d27610dc44d64418e733675e83ac7db561c30611b6d256b14f7aebd6addbdca04183e98cd343b4fd31e3ec483563279b3cea1f691ef54ba1e6b18471dea7765558756b1ea95ee68c925c7baadf1db0531e449598279fdf19d32f7303e5be8b8f8f7f119bffcd9cbb02db8660cfb5e88e46e7f1a14387be46debbb9b999537272323e43becece4e9f939d9dfa535656ca622a590ed62b3d9e8cdf19b3351e4fdabad4d4d40d23478edc74d965976de1d6e1d68b2eba68db902143b6ef8d507e4761d99b3af66719f5dde4a26da1c280b9b055f3cae4b22da1c460d0a0419b2bab14fc7e33f85f55edce79fa67bcbfc5c86d058c00b7a4e585dcc609f33d4eb0cf7382fd9d93ad5b57c3c476276bd21d8151a83499a513b88e7da2b4fd29b2abb0d00f8f6c14c911b9909d905847787c76422cf4debecf212637a6a7a7df026178faf6db6f9f4e7c15b8fcc5ee7d05aa69080ef52169f1ea9e70632e281a54842b58496f1be5a712990ece7a3d1ad18ad918f3fcd5ab576bace640081f672c2791b691399caff1c71eff3cd0f1de5a459deeb750b8bcea8650bbfa35b794e76b03fd3c6cc963fe2563d77cc8eccb5c08dc46faff81f7f590d131e83f077e93293b039db9ccbfa5a46dc163a2b5b4446dab0e08581f0c0143c010288c8091dbc268946f3c1f32b78e13f10b9094d19096e934b79393b77b8f2827640e3d8ee0168ebbc4821dba05b1fd13ecdebeef188fa4efd6793ee4428447647e0b24ee0b88c87d90a03b489f3460c080ef28b30e1cdcd3f7fba717fba5557914732057ee82064c9c11e0e3c2403bca38cf3773e217e29f4f9f3e7d3df10abfd22f6833b569d3a67321e64f422ac733977f636cf3e947117b4893cd81ba556c1e73c3cdffc299d45df862299f76dd720374b6129f0d69fd1fa4f65ae6e0f5c4ef83c8be08199e7af3cd37cfa1be65e8e9a12f7947f43bd31d9322b6926f9b216008180286403543c0c86d91012ddf03489d48df1f2d5bb67c178233ba56ad5a4fd1e2024ec62990875c4ee4eec44b5ee1133a2a7f6d9cb0ff3ad80f31b55f481c59484949d15a21bd747a0b046c3664e3f1c4c4c47f722bfb5fcd9a357b0a4fedec9d3b776e9d3469524d23b5fe11e262660db8e9955d6e4d3563bd8797d2af5c3442b1fcb4e8e8e8998d1a35fa757fafcb5abb766d1a047b41bd7af59e855cde0dc19dcadcdd443c1b52abf9e0fac79c2eda8bbd3b72174afc1eb2c12b19f99d6abe008b47f1ccfe83e3511d3b767c8879f63a73f00be6de2fe4eba9f34cfdd6b0cbfd9e48b3cd1030040c0143a0062160e4763f0cf6f2e5cb77e259fab643870ee31b366cf8109edc3731e317c8811ed6d1ed5279c348aa3c1b44c1913185908a3c2413498664acc5cab9108c572138f7d287bb5bb56af558ebd6addf8408cd5ab972e506110d746af4c62df205781637808f2e70dc6d76485b304ce4a9cc466f36b7d33f7decb1c7b6072b5011f922d88ced1f5cb8bc87c7f401e6c40448eef7b070bd2ac779a765077d55105028bb7bbe08a9e6570ef32b8ddfc61a746630b79ee562f03edab88fdfcd136ddab479134cbe5ab264c9ca55ab5669c9c4eef5d871792060751a028680215005103072bb9f064984effbefbf5fd1a2458b77f0823dca49fc514ee09320048b38a96fe5842e4fe8ffb37777b131a5711cc73bdab24404895b771289b8c29d0b172e444442425c485cb847249be542594ba3dda1ed76bdb58b6e5bf59a6c376ced62852ccbdab5e81bc2b2aac16ab3456bdae9cbccfefeb3081ae9cb9c993967ceb799273367e69ce73ccfe77992f9cd33d3190b0af6649fa256feff31099ddcda605fa1f44aed7caed2a476fea6f61e56f02ad42a6d9e8247c1c2850b4bd6af5f7faaa1a1e15e6d6d6dd2fee149ed73fd452f621e684c6f28e03e1f3d7a746c8573108deed3fe0fb5527962ead4a937972c59622f7c067158e277515fa20a95cfb5627a412f66f66aceeed06a6db5e645936ebf500bba14446dfeeae6c72f568f8a057eeb5b48b75f687e3d5569545d357af1b473cc9831c1c9932717cf9d3bb75c21fa5c5d5dddfdc6c6464fffa0c7c745780401041040205e01c26dbc82711e7feddab5d0b367cfeae6cd9b77504166873d91eb49fd80dec6bea850f34841a143dbf6dd771600629f655468887d3651414067b7211c7e795397c244ac4e9d2b43e7d6ddd15ed5dfa9f6d8e7829f2a6434aa3d67d4c6fdba1dd489f3146a832b56acd8d5dede5eadd5e8c663c78e755868d7635c3e10686e6eb67facab96e11fc2ed54898da58c63eeb6fde6b68d81b6fb340ef625f1a7a64f9ffee3ad5bb7ec230df622e3839a53be19d5d8372f5ebcb87afcf8f1c50ab845eac711cd912b6a997db7739be6b0855dfb584187e699bde8b17f886bd77e76bffd225db3e659bdfafdb34aa58efd4a2f9af2746d1f3928d1aaf799bb77efded7fcb2177c6e345057b9a44680b322800002fd052c15f5bf977b922d103d71e244a8bbbbbb71eddab58766cd9a55100e87b7eac93f574ffafbb45af5cbb871e31e693ba4f0609f5bb555ae5e05200bbc837eb2d7feb120f56ee714a0a20a117d3a47af82478faec30ab0ffe8feeb5a613c190a854a74ee5cb567b38247eef2e5cb836a63a9b66b5a5a5aee969494bcfd62ff77ebe5763f81a8dc7e95ef41d95dd5b876e8b6fd735486fcdfee2c77fb5a351bd756ed7776f6ecd9550b162cb05f08b3fbdeeee7b61b0a9e7d6bd6aca9cdc9c929d53c09767575e5aa8d3667b62aa8166a957f9faeab74df61cda70aed53aaf958a4fb36abff9b64615f66be4d7370bb82f2d79ae7552f5fbebc6aabc33a66d0735cfb724100010410f0b900e1f6f50470cb95ad7c5eba74e9b19eec2f14151595e9893f7fc284099bb47ab54e6160a342c19eb163c7fea010f4a7b66d65cc56c12cf85800184ab1b78c5f29503f5610a9d78ae2390510fb8aa702058cf5babd41e7dc3a67ce9ced5bb66c29557bbe53d8f8bdb2b232f66d076ef1f2523bf2f3f3db57ad5a7552e3662f5e0ea9edf775bb47fef631853e05bd88c6a347c1f7b61e2b9f3163c62eed7fdde684b65d7fb176aa84d49f7b6aec990d1b36942f5ab4a8582bba853d3d3d41cde53ccddf6d9a47f99a4ff65183c2a3478feeececec2c53bfabb5cf65dd7e58515161abbbaa22c3e6b35d5310400001041018b400e176d054c9dfd17ed4a0b5b5f5c9ead5ab2f6b25ebf8a449932cd8ee5078c8cdceceb6afd75a17088cf84c2bba9fabe8ede08cfdda2e57a95239a2724c6f091f51a90c0432f68d1811d8a3dbc1402060fbdbb19f2a70ac53b0cd0904025b14aef2264e9c58ac905175e0c081b30a2a75e7cf9f7faa8015d6e3040d07a6404141c1bf723dabf12cd48b881c85bd7c8d4985c6e0a8aebf51f942abe539d3a64d2b090683d7962e5deab9cf96be992bea67442bbaddcff5a710ff58c1f581e6ee5fba6ddf7af0a4a9a9a94dfdb39fc48dcd2d3bce8a03cc54e1bc0035228000029e1120dc7a60a82c24a899bd2d2d2d1d6d6d6d8f4e9f3e7d7dfefcf9e71416aa23919ef248a477f7c89159c559599905595981ed9999195fbe2e0a4ed9f9999981a01edb919d9d55386a54767124f2c96e1d53aa63bf5db66cd9f1fafafa9f2e5ebc7845abb20d3ac7539dabdb42c7ebf36a938b9302720d69c5f2ceca952bbfd70a66895e541428f46dd775a1cade9a9a9a931b376e7c60df4ae0e47953599785d6774b2adbc2b91140000104d25b20f9e136bd3d13de3b058488851e5b11d3c9ec1fcd5e6925ac5541f7ef70387c5ba5516ff1d6abdc54b9d1ddfdaa56f735a8dc5180baa755c1e668b4ddbed8defe9927545656d63565ca94f0cc99337b2cd05afdaa974b8205ccda3eafacb1d37844eb747d43e58e4a8b5eb884edf1043781ea114000010410484b01c26d1a0cab026954c57e81c9dee2fd58b1c7add8beb1b781d3a0eb9eefc207e3161b1fcf778a0ec425c0c10820800002f109106ee3f3e36804104000010410400081e4080cea2c84db4131b11302082080000208208080170408b75e1825da880002ce0b502302082080405a0a106ed37258e9140208208000020820307c012f1f49b8f5f2e8d17604104000010410400081f70408b7ef71b0810002ce0b5023020820800002c91320dc26cf9a33218000020820800002ef0bb0e5b800e1d671522a44000104104000010410489500e13655f29c1701e705a811010410400001df0b106e7d3f0500400001041040c00f02f4d12f02845bbf8c34fd4400010410400001047c2040b8f5c120d345e705a81101041040000104dc2940b875e7b8d02a041040000104bc2a40bb1148a900e136a5fc9c1c010410400001041040c04901c2ad939ad4e5bc0035228000020820800002431020dc0e018b5d1140000104107093006d410081fe0284dbfe26dc83000208208000020820e05101c2ad4707cef96653230208208000020820e07d01c2adf7c7901e2080000208245a80fa1140c03302845bcf0c150d45000104104000010410184880703b9090f38f532302082080000208208040820408b70982a55a0410400081e108700c020820109f00e1363e3f8e460001041040000104107091405a875b1739d314041040000104104000812408106e9380cc2910400001170ad024041040202d0508b76939ac740a0104104000010410f0a78033e1d69f76f41a010410400001041040c06502845b970d08cd410081f413a0470820800002c91320dc26cf9a3321800002082080000208bc2fe0f816e1d671522a44000104104000010410489500e13655f29c1701049c17a0460410400001df0b106e7d3f050040000104104000013f08f8a58f845bbf8c34fd4400010410400001047c2040b8f5c120d345049c17a046041040000104dc2940b875e7b8d02a04104000010410f0aa00ed4ea900e136a5fc9c1c010410400001041040c04901c2ad939ad48580f302d48800020820800002431020dc0e018b5d11400001041040c04d02b40581fe0284dbfe26dc83000208208000020820e05101c2ad47078e663b2f408d08208000020820e07d01c2adf7c7901e208000020820906801ea47c03302845bcf0c150d45000104104000010410184880703b90108f3b2f408d082080000208208040820408b70982a55a0410400001048623c0310820109f00e1363e3f8e46000104104000010410709100e1d64583e17c53a8110104104000010410f09700e1d65fe34d6f1140000104de08708d0002692940b84dcb61a5530820800002082080803f0508b7ce8c3bb520800002082080000208b8408070eb8241a009082080407a0bd03b04104020790284dbe459732604104000010410400081040b782edc26d883ea114000010410400001043c2c40b8f5f0e0d174041040e003013611400001df0b106e7d3f0500400001041040000104d247e0e3e1367dfa484f104000010410400001047c2240b8f5c940d34d0410705680da1040000104dc29f01f000000ffff17ca06e3000000064944415403003c297dc9432a2cb70000000049454e44ae426082, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:09:56', '2026-07-21 22:24:00', NULL);
INSERT INTO `sys_user` (`id`, `username`, `name`, `title`, `password`, `active`, `role`, `extra`, `email`, `mobile`, `registered_by_name`, `registered_date`, `registered_by`, `digital_signature`, `pin_code`, `login_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(6, 'nurse', 'nurse', NULL, 'd89921949615574a42c781c919fdd913', 1, 'NURSE', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:10:16', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:10:16', '2026-07-21 22:10:16', NULL),
(7, 'laboratory', 'laboratory', NULL, 'd89921949615574a42c781c919fdd913', 1, 'LABORATORY', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:10:54', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:10:54', '2026-07-21 22:10:54', NULL),
(8, 'reception', 'reception', NULL, 'd89921949615574a42c781c919fdd913', 1, 'RECEPTION', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:11:21', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:11:21', '2026-07-21 22:11:21', NULL),
(9, 'finance', 'finance', NULL, 'd89921949615574a42c781c919fdd913', 1, 'FINANCE', '{\"extra_role\":[],\"default_store\":\"\"}', '', '', 'admin', '2026-07-21 20:15:15', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:15:15', '2026-07-21 22:15:15', NULL),
(10, 'store', 'store', NULL, 'd89921949615574a42c781c919fdd913', 1, 'DISPENSARY', '{\"extra_role\":[],\"default_store\":\"1\"}', '', '', 'admin', '2026-07-21 20:18:27', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:18:27', '2026-07-21 22:18:27', NULL),
(11, 'dispensary', 'dispensary', NULL, 'd89921949615574a42c781c919fdd913', 1, 'DISPENSARY', '{\"extra_role\":[],\"default_store\":\"2\"}', '', '', 'admin', '2026-07-21 20:18:48', 1, NULL, '4a7d1ed414474e4033ac29ccb8653d9b', NULL, '2026-07-21 22:18:48', '2026-07-21 22:18:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sys_user_log`
--

CREATE TABLE `sys_user_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime NOT NULL,
  `agent_device` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sys_user_log`
--

INSERT INTO `sys_user_log` (`id`, `user_id`, `login_time`, `logout_time`, `agent_device`, `ip_address`) VALUES
(1, 1, '2026-07-21 08:38:42', '2026-07-21 08:48:00', 'Chrome 150.0.0.0 Windows 10 ', '196.188.243.31'),
(2, 1, '2026-07-21 08:48:27', '0000-00-00 00:00:00', 'Chrome 150.0.0.0 Windows 10 ', '196.188.243.31'),
(3, 1, '2026-07-21 09:55:07', '0000-00-00 00:00:00', 'Chrome 150.0.0.0 Windows 10 ', '196.188.243.31'),
(4, 1, '2026-07-21 11:23:12', '0000-00-00 00:00:00', 'Edge 149.0.0.0 Android Android', '196.188.252.138'),
(5, 1, '2026-07-22 09:56:57', '2026-07-22 09:57:10', 'Chrome 150.0.0.0 Windows 10 ', '196.190.181.21'),
(6, 1, '2026-07-25 03:44:42', '0000-00-00 00:00:00', 'Chrome 150.0.0.0 Windows 10 ', '196.188.231.24'),
(7, 1, '2026-08-01 06:32:57', '2026-08-01 06:35:52', 'Edge 150.0.0.0 Android Android', '196.188.252.30'),
(8, 1, '2026-08-01 06:48:34', '0000-00-00 00:00:00', 'Edge 150.0.0.0 Android Android', '196.188.252.30'),
(9, 1, '2026-08-02 04:22:22', '0000-00-00 00:00:00', 'Safari 604.1 iOS Apple iPhone', '196.189.27.77'),
(10, 1, '2026-08-07 01:24:32', '0000-00-00 00:00:00', 'Edge 150.0.0.0 Android Android', '196.188.98.232'),
(11, 1, '2026-08-07 10:11:00', '0000-00-00 00:00:00', 'Edge 151.0.0.0 Windows 10 ', '196.189.127.139'),
(12, 1, '2026-08-07 10:22:16', '0000-00-00 00:00:00', 'Edge 151.0.0.0 Windows 10 ', '196.189.127.139'),
(13, 1, '2026-08-07 10:22:58', '2026-08-07 10:39:36', 'Edge 151.0.0.0 Windows 10 ', '196.189.127.139'),
(14, 1, '2026-08-07 10:39:50', '2026-08-07 10:42:46', 'Edge 151.0.0.0 Windows 10 ', '196.189.127.139'),
(15, 1, '2026-08-10 05:45:23', '0000-00-00 00:00:00', 'Chrome 151.0.0.0 Windows 10 ', '196.190.62.247'),
(16, 1, '2026-11-02 12:00:19', '2026-11-02 12:05:06', 'Edge 155.0.0.0 Windows 10 ', '196.191.254.17'),
(17, 1, '2026-11-02 12:05:09', '0000-00-00 00:00:00', 'Edge 155.0.0.0 Windows 10 ', '196.191.254.17'),
(18, 1, '2026-11-02 01:32:25', '0000-00-00 00:00:00', 'Safari 604.1 iOS Apple iPhone', '196.190.62.253'),
(19, 1, '2026-11-02 02:38:19', '0000-00-00 00:00:00', 'Chrome 135.0.7049.79 Android Android', '176.3.167.130'),
(20, 1, '2026-11-02 03:08:14', '0000-00-00 00:00:00', 'Edge 155.0.0.0 Android Android', '196.191.254.17'),
(21, 1, '2026-11-02 03:13:50', '0000-00-00 00:00:00', 'Safari 604.1 iOS Apple iPhone', '196.191.221.193'),
(22, 1, '2026-11-02 03:14:31', '0000-00-00 00:00:00', 'Edge 155.0.0.0 Android Android', '196.191.254.17'),
(23, 1, '2026-11-02 11:09:09', '0000-00-00 00:00:00', 'Edge 155.0.0.0 Windows 10 ', '196.189.145.228'),
(24, 1, '2026-11-03 10:00:24', '0000-00-00 00:00:00', 'Safari 604.1 iOS Apple iPhone', '196.191.60.80'),
(25, 1, '2026-11-03 01:20:41', '0000-00-00 00:00:00', 'Edge 155.0.0.0 Windows 10 ', '196.191.254.17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_item_exp_temp`
-- (See below for the actual view)
--
CREATE TABLE `view_item_exp_temp` (
`id` int(11)
,`item_id` int(11)
,`expiry_date` varchar(10)
,`quantity` float
,`new_ex_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `_average_cost`
--

CREATE TABLE `_average_cost` (
  `id` int(11) NOT NULL,
  `last_char` varchar(5) NOT NULL,
  `peachtree_id` varchar(20) NOT NULL,
  `average_cost` float NOT NULL,
  `item_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `_from_peachtree`
--

CREATE TABLE `_from_peachtree` (
  `peachtree_id` varchar(30) NOT NULL,
  `bluelime_id` varchar(30) DEFAULT NULL,
  `bluelime_id_full` varchar(20) DEFAULT NULL,
  `item_description` varchar(40) NOT NULL,
  `quantity_on_hand` float DEFAULT 0,
  `item_value` float DEFAULT 0,
  `average_cost` float DEFAULT 0,
  `location` varchar(3) DEFAULT NULL,
  `flag` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `_peachtree_items_all`
--

CREATE TABLE `_peachtree_items_all` (
  `id` int(11) NOT NULL,
  `peachtree_code` varchar(30) NOT NULL,
  `peachtree_discription` varchar(30) NOT NULL,
  `location` varchar(30) NOT NULL,
  `bluelime_code` varchar(30) NOT NULL,
  `remark` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hospital_allergies`
--
ALTER TABLE `hospital_allergies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_approved_doctors_report`
--
ALTER TABLE `hospital_approved_doctors_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_auto_complete`
--
ALTER TABLE `hospital_auto_complete`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_bills`
--
ALTER TABLE `hospital_bills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_bill_details`
--
ALTER TABLE `hospital_bill_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_bill_payments`
--
ALTER TABLE `hospital_bill_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_biopsy_result`
--
ALTER TABLE `hospital_biopsy_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_chief_complaints`
--
ALTER TABLE `hospital_chief_complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_company`
--
ALTER TABLE `hospital_company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_compounding_prescription_detail`
--
ALTER TABLE `hospital_compounding_prescription_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_compounding_prescription_main`
--
ALTER TABLE `hospital_compounding_prescription_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_compounding_templet`
--
ALTER TABLE `hospital_compounding_templet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_derma_medical_history`
--
ALTER TABLE `hospital_derma_medical_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_diagnoses`
--
ALTER TABLE `hospital_diagnoses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_diseases`
--
ALTER TABLE `hospital_diseases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_drugs`
--
ALTER TABLE `hospital_drugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_examination_extra_oral`
--
ALTER TABLE `hospital_examination_extra_oral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_examination_intra_oral`
--
ALTER TABLE `hospital_examination_intra_oral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_external_documents`
--
ALTER TABLE `hospital_external_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_follow_up`
--
ALTER TABLE `hospital_follow_up`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_laboratory_results`
--
ALTER TABLE `hospital_laboratory_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_laboratory_result_attributes`
--
ALTER TABLE `hospital_laboratory_result_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_lab_requests`
--
ALTER TABLE `hospital_lab_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_medical_certificate`
--
ALTER TABLE `hospital_medical_certificate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_medical_history`
--
ALTER TABLE `hospital_medical_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_medical_report`
--
ALTER TABLE `hospital_medical_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_message`
--
ALTER TABLE `hospital_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_nurse_history`
--
ALTER TABLE `hospital_nurse_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_order_detail`
--
ALTER TABLE `hospital_order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_order_main`
--
ALTER TABLE `hospital_order_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_order_options`
--
ALTER TABLE `hospital_order_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_order_status`
--
ALTER TABLE `hospital_order_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_patients`
--
ALTER TABLE `hospital_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_payment_request`
--
ALTER TABLE `hospital_payment_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_petty_cash`
--
ALTER TABLE `hospital_petty_cash`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_prescriptions`
--
ALTER TABLE `hospital_prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_prescription_auto_fill`
--
ALTER TABLE `hospital_prescription_auto_fill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_prescription_detail`
--
ALTER TABLE `hospital_prescription_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_pricelist`
--
ALTER TABLE `hospital_pricelist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_pricelist_detail`
--
ALTER TABLE `hospital_pricelist_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_procedures`
--
ALTER TABLE `hospital_procedures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_procedure_attributes`
--
ALTER TABLE `hospital_procedure_attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_procedure_results`
--
ALTER TABLE `hospital_procedure_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_progress_picture`
--
ALTER TABLE `hospital_progress_picture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_schedule`
--
ALTER TABLE `hospital_schedule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_services`
--
ALTER TABLE `hospital_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_skip_date`
--
ALTER TABLE `hospital_skip_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_template`
--
ALTER TABLE `hospital_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_treatments`
--
ALTER TABLE `hospital_treatments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_visits`
--
ALTER TABLE `hospital_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_visit_encounter`
--
ALTER TABLE `hospital_visit_encounter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_vital_sign`
--
ALTER TABLE `hospital_vital_sign`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_waiting_list`
--
ALTER TABLE `hospital_waiting_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_xray`
--
ALTER TABLE `hospital_xray`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_credit_payment_invoice`
--
ALTER TABLE `inventory_credit_payment_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_daily_sales_export`
--
ALTER TABLE `inventory_daily_sales_export`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_expiry_date_temp`
--
ALTER TABLE `inventory_expiry_date_temp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_finance_maintain`
--
ALTER TABLE `inventory_finance_maintain`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_grv_detail`
--
ALTER TABLE `inventory_grv_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_grv_main`
--
ALTER TABLE `inventory_grv_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_inventory_count_setup`
--
ALTER TABLE `inventory_inventory_count_setup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_item_category`
--
ALTER TABLE `inventory_item_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_item_count_record`
--
ALTER TABLE `inventory_item_count_record`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_item_location`
--
ALTER TABLE `inventory_item_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_peachtree_item`
--
ALTER TABLE `inventory_peachtree_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_pricelist`
--
ALTER TABLE `inventory_pricelist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_purchase_detail`
--
ALTER TABLE `inventory_purchase_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_purchase_main`
--
ALTER TABLE `inventory_purchase_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_sales_detail`
--
ALTER TABLE `inventory_sales_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_sales_detail_old`
--
ALTER TABLE `inventory_sales_detail_old`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_sales_main`
--
ALTER TABLE `inventory_sales_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_siv_detail`
--
ALTER TABLE `inventory_siv_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_siv_main`
--
ALTER TABLE `inventory_siv_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_srv_detail`
--
ALTER TABLE `inventory_srv_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_srv_main`
--
ALTER TABLE `inventory_srv_main`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_stores`
--
ALTER TABLE `inventory_stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_supplier`
--
ALTER TABLE `inventory_supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_prescription`
--
ALTER TABLE `online_prescription`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_bill`
--
ALTER TABLE `pos_bill`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_cashier_summary`
--
ALTER TABLE `pos_cashier_summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_machine`
--
ALTER TABLE `pos_machine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos_maraki_void`
--
ALTER TABLE `pos_maraki_void`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_api`
--
ALTER TABLE `sys_api`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_header_information`
--
ALTER TABLE `sys_header_information`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_measurement`
--
ALTER TABLE `sys_measurement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_notification`
--
ALTER TABLE `sys_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sys_user`
--
ALTER TABLE `sys_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `sys_user_log`
--
ALTER TABLE `sys_user_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `_average_cost`
--
ALTER TABLE `_average_cost`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `_from_peachtree`
--
ALTER TABLE `_from_peachtree`
  ADD PRIMARY KEY (`peachtree_id`);

--
-- Indexes for table `_peachtree_items_all`
--
ALTER TABLE `_peachtree_items_all`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hospital_allergies`
--
ALTER TABLE `hospital_allergies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_approved_doctors_report`
--
ALTER TABLE `hospital_approved_doctors_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_auto_complete`
--
ALTER TABLE `hospital_auto_complete`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hospital_bills`
--
ALTER TABLE `hospital_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_bill_details`
--
ALTER TABLE `hospital_bill_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_bill_payments`
--
ALTER TABLE `hospital_bill_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_biopsy_result`
--
ALTER TABLE `hospital_biopsy_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_chief_complaints`
--
ALTER TABLE `hospital_chief_complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_company`
--
ALTER TABLE `hospital_company`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_compounding_prescription_detail`
--
ALTER TABLE `hospital_compounding_prescription_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_compounding_prescription_main`
--
ALTER TABLE `hospital_compounding_prescription_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_compounding_templet`
--
ALTER TABLE `hospital_compounding_templet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_derma_medical_history`
--
ALTER TABLE `hospital_derma_medical_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_diagnoses`
--
ALTER TABLE `hospital_diagnoses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_diseases`
--
ALTER TABLE `hospital_diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_drugs`
--
ALTER TABLE `hospital_drugs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_examination_extra_oral`
--
ALTER TABLE `hospital_examination_extra_oral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_examination_intra_oral`
--
ALTER TABLE `hospital_examination_intra_oral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_external_documents`
--
ALTER TABLE `hospital_external_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_follow_up`
--
ALTER TABLE `hospital_follow_up`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_laboratory_results`
--
ALTER TABLE `hospital_laboratory_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_laboratory_result_attributes`
--
ALTER TABLE `hospital_laboratory_result_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_lab_requests`
--
ALTER TABLE `hospital_lab_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_medical_certificate`
--
ALTER TABLE `hospital_medical_certificate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_medical_history`
--
ALTER TABLE `hospital_medical_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_medical_report`
--
ALTER TABLE `hospital_medical_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_message`
--
ALTER TABLE `hospital_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_nurse_history`
--
ALTER TABLE `hospital_nurse_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_order_detail`
--
ALTER TABLE `hospital_order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_order_main`
--
ALTER TABLE `hospital_order_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_order_options`
--
ALTER TABLE `hospital_order_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_order_status`
--
ALTER TABLE `hospital_order_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_patients`
--
ALTER TABLE `hospital_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_payment_request`
--
ALTER TABLE `hospital_payment_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_petty_cash`
--
ALTER TABLE `hospital_petty_cash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_prescriptions`
--
ALTER TABLE `hospital_prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_prescription_auto_fill`
--
ALTER TABLE `hospital_prescription_auto_fill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_prescription_detail`
--
ALTER TABLE `hospital_prescription_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_pricelist`
--
ALTER TABLE `hospital_pricelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_pricelist_detail`
--
ALTER TABLE `hospital_pricelist_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hospital_procedures`
--
ALTER TABLE `hospital_procedures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_procedure_attributes`
--
ALTER TABLE `hospital_procedure_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_procedure_results`
--
ALTER TABLE `hospital_procedure_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_progress_picture`
--
ALTER TABLE `hospital_progress_picture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_schedule`
--
ALTER TABLE `hospital_schedule`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_services`
--
ALTER TABLE `hospital_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hospital_skip_date`
--
ALTER TABLE `hospital_skip_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_template`
--
ALTER TABLE `hospital_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_treatments`
--
ALTER TABLE `hospital_treatments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_visits`
--
ALTER TABLE `hospital_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_visit_encounter`
--
ALTER TABLE `hospital_visit_encounter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_vital_sign`
--
ALTER TABLE `hospital_vital_sign`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_waiting_list`
--
ALTER TABLE `hospital_waiting_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hospital_xray`
--
ALTER TABLE `hospital_xray`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_credit_payment_invoice`
--
ALTER TABLE `inventory_credit_payment_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_daily_sales_export`
--
ALTER TABLE `inventory_daily_sales_export`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_expiry_date_temp`
--
ALTER TABLE `inventory_expiry_date_temp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_finance_maintain`
--
ALTER TABLE `inventory_finance_maintain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_grv_detail`
--
ALTER TABLE `inventory_grv_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_grv_main`
--
ALTER TABLE `inventory_grv_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_inventory_count_setup`
--
ALTER TABLE `inventory_inventory_count_setup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_item_category`
--
ALTER TABLE `inventory_item_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_item_count_record`
--
ALTER TABLE `inventory_item_count_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_item_location`
--
ALTER TABLE `inventory_item_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_peachtree_item`
--
ALTER TABLE `inventory_peachtree_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_pricelist`
--
ALTER TABLE `inventory_pricelist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_purchase_detail`
--
ALTER TABLE `inventory_purchase_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_purchase_main`
--
ALTER TABLE `inventory_purchase_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_sales_detail`
--
ALTER TABLE `inventory_sales_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_sales_detail_old`
--
ALTER TABLE `inventory_sales_detail_old`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_sales_main`
--
ALTER TABLE `inventory_sales_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_siv_detail`
--
ALTER TABLE `inventory_siv_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_siv_main`
--
ALTER TABLE `inventory_siv_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_srv_detail`
--
ALTER TABLE `inventory_srv_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_srv_main`
--
ALTER TABLE `inventory_srv_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_stores`
--
ALTER TABLE `inventory_stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_supplier`
--
ALTER TABLE `inventory_supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_prescription`
--
ALTER TABLE `online_prescription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_bill`
--
ALTER TABLE `pos_bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_cashier_summary`
--
ALTER TABLE `pos_cashier_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_machine`
--
ALTER TABLE `pos_machine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_maraki_void`
--
ALTER TABLE `pos_maraki_void`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_api`
--
ALTER TABLE `sys_api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_header_information`
--
ALTER TABLE `sys_header_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sys_measurement`
--
ALTER TABLE `sys_measurement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sys_notification`
--
ALTER TABLE `sys_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sys_user`
--
ALTER TABLE `sys_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sys_user_log`
--
ALTER TABLE `sys_user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `_average_cost`
--
ALTER TABLE `_average_cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `_peachtree_items_all`
--
ALTER TABLE `_peachtree_items_all`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Structure for view `avg_patient_tat_per_minuite`
--
DROP TABLE IF EXISTS `avg_patient_tat_per_minuite`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `avg_patient_tat_per_minuite`  AS SELECT 'WithOutAppointment' AS `WithOutAppointment`, `hospital_visits`.`created_at` AS `created_at`, `hospital_visits`.`doctor_name` AS `doctor_name`, avg(timestampdiff(MINUTE,`hospital_visits`.`created_at`,`hospital_visit_encounter`.`created_at`)) AS `UpToDoctor`, avg(timestampdiff(MINUTE,`hospital_visits`.`created_at`,`hospital_visit_encounter`.`updated_at`)) AS `TotalStayedTime` FROM (`hospital_visits` join `hospital_visit_encounter` on(`hospital_visits`.`id` = `hospital_visit_encounter`.`visit_id`)) WHERE `hospital_visits`.`appointment_id` = 0 GROUP BY `hospital_visits`.`doctor_name`union all select 'WithAppointment' AS `WithAppointment`,`hospital_visits`.`created_at` AS `created_at`,`hospital_visits`.`doctor_name` AS `doctor_name`,avg(timestampdiff(MINUTE,`hospital_visits`.`created_at`,`hospital_visit_encounter`.`created_at`)) AS `UpToDoctor`,avg(timestampdiff(MINUTE,`hospital_visits`.`created_at`,`hospital_visit_encounter`.`updated_at`)) AS `TotalStayedTime` from (`hospital_visits` join `hospital_visit_encounter` on(`hospital_visits`.`id` = `hospital_visit_encounter`.`visit_id`)) where `hospital_visits`.`appointment_id` <> 0 group by `hospital_visits`.`doctor_name`  ;

-- --------------------------------------------------------

--
-- Structure for view `hospital_view_bill_and_payment`
--
DROP TABLE IF EXISTS `hospital_view_bill_and_payment`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `hospital_view_bill_and_payment`  AS SELECT `hospital_bill_details`.`bill_id` AS `bill_id`, `hospital_bill_details`.`service_type` AS `service_type`, round(sum(`hospital_bill_details`.`bill_quantity` * `hospital_bill_details`.`bill_amount` - `hospital_bill_details`.`bill_discount` * `hospital_bill_details`.`bill_quantity` * `hospital_bill_details`.`bill_amount` / 100),2) AS `amount`, convert('BILL' using utf8mb4) AS `type` FROM (`hospital_bill_details` join `hospital_bills` on(`hospital_bill_details`.`bill_id` = `hospital_bills`.`id`)) WHERE `hospital_bills`.`bill_status` <> 3 AND `hospital_bill_details`.`service_status` <> -1 GROUP BY `hospital_bill_details`.`service_type`, `hospital_bill_details`.`bill_id`union select `hospital_bill_payments`.`bill_id` AS `bill_id`,'PAYMENT' AS `service_type`,round(sum(`hospital_bill_payments`.`payed_amount` * -1),2) AS `amount`,convert('PAYMENT' using utf8mb4) AS `type` from `hospital_bill_payments` group by `hospital_bill_payments`.`bill_id`  ;

-- --------------------------------------------------------

--
-- Structure for view `hospital_view_doctors_income`
--
DROP TABLE IF EXISTS `hospital_view_doctors_income`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `hospital_view_doctors_income`  AS SELECT `hospital_bill_payments`.`bill_id` AS `bill_id`, `hospital_visits`.`doctor_name` AS `doctor_name`, `hospital_visits`.`doctor_id` AS `doctor_id`, `hospital_bill_payments`.`payed_amount` AS `payed_amount`, `hospital_bill_payments`.`payed_date` AS `payed_date`, `hospital_bill_payments`.`patient_name` AS `patient_name` FROM ((`hospital_bill_payments` join `hospital_bills` on(`hospital_bill_payments`.`bill_id` = `hospital_bills`.`id`)) join `hospital_visits` on(`hospital_bills`.`visit_id` = `hospital_visits`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `inventory_sales_total1`
--
DROP TABLE IF EXISTS `inventory_sales_total1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `inventory_sales_total1`  AS SELECT `inventory_sales_main`.`fs_number` AS `fs_number`, `inventory_sales_main`.`pos_bill_id` AS `pos_bill_id`, sum((`inventory_sales_detail`.`unit_price` + `inventory_sales_detail`.`vat`) * `inventory_sales_detail`.`quantity`) AS `total`, `inventory_sales_main`.`updated_at` AS `updated_at`, `pos_bill`.`type` AS `type`, `pos_bill`.`payment_method` AS `payment_method` FROM ((`inventory_sales_main` join `inventory_sales_detail` on(`inventory_sales_main`.`id` = `inventory_sales_detail`.`sales_id`)) join `pos_bill` on(`inventory_sales_main`.`pos_bill_id` = `pos_bill`.`id`)) WHERE `inventory_sales_main`.`deleted_at` is null GROUP BY `inventory_sales_main`.`fs_number`, `inventory_sales_main`.`pos_bill_id`, `inventory_sales_main`.`updated_at` ;

-- --------------------------------------------------------

--
-- Structure for view `manual_sales_temp`
--
DROP TABLE IF EXISTS `manual_sales_temp`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `manual_sales_temp`  AS SELECT `pos_bill`.`id` AS `id`, `pos_bill`.`invoice_no` AS `invoice_no`, `pos_bill`.`pos_bill_json` AS `pos_bill_json`, `pos_bill`.`total` AS `total`, `pos_bill`.`sales_id_list` AS `sales_id_list`, `pos_bill`.`type` AS `type`, `pos_bill`.`response_data` AS `response_data`, `pos_bill`.`fs_number` AS `fs_number`, `pos_bill`.`machine_number` AS `machine_number`, `pos_bill`.`payment_method` AS `payment_method`, `pos_bill`.`transferred_amount` AS `transferred_amount`, `pos_bill`.`payment_reference` AS `payment_reference`, `pos_bill`.`sent_to_pos_date` AS `sent_to_pos_date`, `pos_bill`.`response_date` AS `response_date`, `pos_bill`.`created_at` AS `created_at`, `pos_bill`.`updated_at` AS `updated_at`, `pos_bill`.`deleted_at` AS `deleted_at` FROM `pos_bill` WHERE (`pos_bill`.`created_at` >= '2024-10-11' OR `pos_bill`.`created_at` <= '2024-11-09') AND `pos_bill`.`type` = 'Inventory' AND `pos_bill`.`fs_number` like 'm%' AND `pos_bill`.`deleted_at` is null ORDER BY `pos_bill`.`id` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_item_exp_temp`
--
DROP TABLE IF EXISTS `view_item_exp_temp`;

CREATE ALGORITHM=UNDEFINED DEFINER=`bluelimeplccom`@`localhost` SQL SECURITY DEFINER VIEW `view_item_exp_temp`  AS SELECT `main_tbl`.`id` AS `id`, `main_tbl`.`item_id` AS `item_id`, `main_tbl`.`expiry_date` AS `expiry_date`, `main_tbl`.`quantity` AS `quantity`, `main_tbl`.`new_ex_date` AS `new_ex_date` FROM (`inventory_expiry_date_temp` `main_tbl` join (select max(`inventory_expiry_date_temp`.`id`) AS `id`,`inventory_expiry_date_temp`.`item_id` AS `item_id` from `inventory_expiry_date_temp` group by `inventory_expiry_date_temp`.`item_id`) `filter` on(`main_tbl`.`id` = `filter`.`id`)) WHERE `main_tbl`.`new_ex_date` is not null ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
