<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    $baseUrl = "https://www.ujalahappiestonam.com";
    $masterKey = "803672140";
    $cookie = "casdbury-blockbuster-id=803672140";
    
    $userAgents = [
        "Mozilla/5.0 (Linux; Android 13; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Mobile Safari/537.36",
        "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1",
        "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 13_4) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.4 Safari/605.1.15",
        "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36"
    ];
    $userAgent = $userAgents[array_rand($userAgents)];

    function sendRequest($url, $postData, $headers, $isMultipart = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, ""); 
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $httpCode, 'response' => json_decode($response, true) ?? $response];
    }

    function generateSignedData($payload, $userKey, $dataKey) {
        $t = round(microtime(true) * 1000);
        $payload['userKey'] = (int)$userKey;
        $payload['t'] = $t;
        
        $p = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $a = base64_encode($p);
        $u = base64_encode((string)$t);
        
        $padded_key = (string)$dataKey . str_repeat("0", 18);
        $key_material = substr($padded_key, 4, 14); 
        
        $sign_base = $u . "." . $a;
        $h = hash_hmac('sha256', $sign_base, $key_material); 
        
        $f_val = base64_encode($h);
        $hex_chars = 'ABCDEF0123456789';
        $random_hex = '';
        for ($i = 0; $i < 4; $i++) {
            $random_hex .= $hex_chars[rand(0, 15)];
        }
        
        $g = "43" . substr($f_val, 0, 3) . $random_hex . substr($f_val, 3);
        
        return [
            't' => $t,
            'signedString' => $u . "." . $a . "." . $g
        ];
    }

    function decryptResponse($respStr) {
        if (!$respStr) return null;
        $decoded = base64_decode($respStr);
        return json_decode($decoded, true);
    }

    if ($action === 'register') {
        $mobile = trim($_POST['mobile'] ?? '');

        if (empty($mobile)) {
            echo json_encode(['status' => 'error', 'msg' => 'Please enter a valid mobile number.']);
            exit;
        }

        $firstNames = explode(',', 'Aarav,Vihaan,Arjun,Sai,Reyansh,Ayaan,Krishna,Ishaan,Shaurya,Atharva,Advik,Pranav,Rudra,Yash,Kabir,Ansh,Aarush,Ayush,Dhruv,Karthik,Rahul,Rohan,Amit,Mohit,Deepak,Vikram,Sanjay,Raj,Karan,Vishal,Aditya,Abhishek,Praveen,Nitin,Sachin,Manish,Sunil,Anil,Mukesh,Rajesh,Priya,Kavya,Anjali,Neha,Pooja,Swati,Riya,Aarti,Nisha,Kiran,Divya,Anita,Sneha,Roshni,Simran,Megha,Kajal,Preeti,Shruti,Asha,Sonam,Shweta,Meena,Jyoti,Rekha,Geeta,Sushma,Sunita,Vandana,Mamta,Sarita,Poonam,Bhavna,Komal,Shikha,Amrita,Suman,Radha,Gauri,Tanvi,Nandini,Tara,Avni,Khushi,Rachna,Pallavi,Alka,Neelam,Savita,Archana,Seema,Lata,Usha,Maya,Madhu,Bina,Malti,Leela,Kamla,Vimala,Shanti,Aakash,Akash,Alok,Amar,Amir,Anand,Ankur,Anuj,Arun,Ashish,Ashok,Atul,Bharat,Bhaskar,Bhavin,Bhuvan,Chandan,Chetan,Chirag,Darshan,Dev,Dinesh,Gagan,Ganesh,Gaurav,Girish,Gopal,Govind,Hari,Harish,Hemant,Hitesh,Jagdish,Jai,Jatin,Javed,Jayant,Jeevan,Jignesh,Jitendra,Kamal,Kapil,Kishore,Kunal,Lalit,Laxman,Lokesh,Madan,Mahesh,Manoj,Mayur,Milan,Milind,Naresh,Naveen,Nawaz,Nikhil,Nilesh,Nirav,Nishant,Nitesh,Om,Omkar,Pankaj,Parag,Param,Paresh,Partha,Pawan,Piyush,Prabhat,Pradeep,Prakash,Pramod,Prasant,Prashant,Pratap,Pratik,Pravin,Prem,Puneet,Raghav,Rajat,Rajeev,Rajendra,Rajiv,Rakesh,Ram,Ramesh,Ranjit,Ravi,Ravindra,Rishi,Ritesh,Rohit,Sagar,Sahil,Samir,Sandeep,Santosh,Saurabh,Saurav,Shankar,Sharad,Shashank,Shashi,Shiv,Shivam,Shyam,Siddharth,Sohan,Subhash,Sudhir,Sujit,Sumit,Suraj,Surya,Sushil,Tarun,Tejas,Tushar,Uday,Udit,Umesh,Upendra,Utkarsh,Vaibhav,Varun,Vasant,Ved,Vidyut,Vijay,Vikas,Vimal,Vinay,Vineet,Vinod,Vipin,Vipul,Virendra,Vishwas,Vivek,Yogesh,Abha,Aditi,Aishwarya,Akanksha,Akshata,Alisha,Amita,Anamika,Ananya,Ankita,Annu,Anushka,Aparna,Arpita,Aruna,Bhakti,Bhavana,Bhavya,Bhumika,Bindu,Chaitra,Chanda,Chandni,Charu,Chetna,Daksha,Damini,Darshana,Deepa,Deepali,Deepika,Devi,Dhanashree,Diksha,Disha,Gargi,Garima,Gauravi,Gayatri,Geetanjali,Gita,Gunjan,Harshita,Hema,Hemlata,Hina,Isha,Ishita,Jaya,Jayashree,Jui,Jyotsna,Kalyani,Kamini,Kanchana,Kavita,Ketaki,Kinjal,Kirti,Kripa,Krutika,Kshama,Kumkum,Kusum,Lavanya,Laxmi,Lina,Madhavi,Madhumita,Madhuri,Mahi,Malati,Malini,Manali,Mandakini,Mangala,Manisha,Manjari,Manjula,Manjusha,Mansi,Mayuri,Meenakshi,Meera,Mita,Mohini,Mridula,Mrunal,Mugdha,Mukta,Naina,Namita,Namrata,Nanda,Nandita,Narayani,Narmada,Nayana,Neelima,Neema,Nidhi,Nikita,Nilima,Nirali,Nirmala,Nirupama,Nishita,Niti,Nupur,Nutan,Ojaswini,Padma,Padmini,Pampa,Panchali,Pankaja,Pari,Paridhi,Parul,Payal,Poornima,Prabha,Prachi,Pradnya,Pragati,Pragya,Prakruti,Pramila,Pranali,Pranjal,Prarthana,Prashanti,Pratiksha,Pratima,Prerana,Priyanka,Pujita,Punita,Purnima,Purva,Pushpa,Rachana,Radhika,Ragini,Rajani,Rajashree,Rajasi,Rajni,Rakhi,Rama,Ramya,Rani,Ranjana,Rashmi,Rati,Ratna,Raveena,Reena,Renu,Renuka,Reshma,Revati,Richa,Riddhi,Ritu,Rohini,Roma,Roopa,Ruchi,Ruchira,Rupal,Rupali,Sadhana,Sagari,Sahana,Sakshi,Saloni,Samiksha,Samita,Sampada,Samyukta,Sana,Sananda,Sanchita,Sandhya,Sangeeta,Sanjana,Sanjivani,Sanskriti,Sapna,Saraswati,Sarika,Satyavati,Savitri,Sejal,Shabana,Shaila,Shailaja,Shalini,Shamita,Sharada,Sharanya,Sharmila,Sharmistha,Shilpa,Shilpi,Shipra,Shivani,Shraddha,Shravani,Shree,Shreya,Shubha,Shubhangi,Siddhi,Smita,Smriti,Snehal,Snigdha,Sonali,Sonia,Spoorthi,Srilata,Srishti,Stuti,Subhashini,Sucheta,Sudha,Suhasini,Sujata,Sukanya,Sulabha,Sulochana,Sumati,Sunanda,Supriya,Surabhi,Surekha,Sushanti,Sushila,Swara,Tamanna,Tanuja,Tanushree,Tapaswini,Taruna,Tejal,Tejaswini,Trishna,Trupti,Trusha,Tulasi,Udaya,Ujwala,Uma,Unnati,Upasana,Urmi,Urmila,Urvashi,Ushma,Utkarsha,Vaidehi,Vaishali,Vaishnavi,Vanita,Varsha,Vasanti,Vasudha,Vasundhara,Veena,Vibha,Vidya,Vidyullata,Vijayalaxmi,Vijaya,Vikalp,Vilina,Vinita,Vinuta,Vishakha,Vrushali,Yamini,Yamuna,Yashoda,Yashomati,Yashwini,Yogita,Yogmini');
        $lastNames = explode(',', 'Sharma,Verma,Nair,Menon,Kumar,Singh,Gupta,Patel,Das,Reddy,Joshi,Chauhan,Thakur,Bhatia,Yadav,Rao,Pandey,Mishra,Tiwari,Mehta,Bose,Pillai,Iyer,Panicker,Rajan,Sen,Roy,Kapoor,Malhotra,Agarwal,Srivastava,Chaudhary,Saxena,Dubey,Garg,Jain,Kaur,Khatri,Mukherjee,Banerjee,Chatterjee,Gosh,Dutta,Desai,Shah,Parekh,Bhatt,Acharya,Deshmukh,Kulkarni,Hedge,Mahajan,Mali,Shetty,Naik,Patil,Jadhav,Chavan,Pawar,Gaikwad,More,Shinde,Kadam,Sutar,Bhosale,Kale,Deshpande,Dixit,Apte,Pradhan,Oza,Oak,Godbole,Ranade,Bhide,Kelkar,Gokhale,Agashe,Bhagwat,Bapat,Pendse,Gore,Kanitkar,Kamat,Phadke,Thakkar,Doshi,Dalal,Choksi,Merchant,Kapadia,Maniar,Somaiya,Vyas,Trivedi,Rawal,Dave,Pathak,Giri,Bhandari,Abbas,Abdullah,Abraham,Achar,Adani,Adhikari,Adiga,Advani,Ahluwalia,Ahmad,Ahmed,Ahuja,Aiyer,Akhtar,Ali,Alva,Alvares,Amin,Amir,Anand,Ananth,Andrade,Ansari,Arora,Arya,Aslam,Asthana,Atre,Awasthi,Ayyangar,Azad,Babu,Bachan,Bachchan,Badami,Bader,Bagchi,Bagga,Bagh,Bahadur,Baidya,Baig,Bains,Bajaj,Bajpai,Bakhshi,Bakshi,Bal,Bala,Balakrishnan,Balan,Balasubramanian,Baliga,Bandi,Bandopadhyay,Banerji,Banga,Bansal,Bapatla,Barad,Baral,Baria,Barman,Barua,Baruah,Basa,Basak,Basu,Batra,Bawa,Bedi,Behera,Behl,Belur,Benegal,Beniwal,Bera,Beri,Bhagat,Bhairav,Bhajan,Bhakta,Bhalerao,Bhalla,Bhandarkar,Bhanot,Bhanushali,Bharadwaj,Bharat,Bharati,Bhardwaj,Bhargava,Bhasin,Bhat,Bhatnagar,Bhattacharjee,Bhattacharya,Bhatti,Bhave,Bhavsar,Bhawal,Bhoi,Bhoite,Bhola,Bhonsle,Bhowmick,Bhowmik,Bhullar,Bhushan,Bhuta,Bhutani,Biju,Billa,Bisht,Biswas,Bohra,Bora,Borah,Bordoloi,Borkar,Boro,Brahma,Brahmbhatt,Buch,Chacko,Chadha,Chahal,Chakrabarti,Chakraborty,Chakravarty,Chakravarthy,Chalukya,Chaman,Chamoli,Champatiray,Chand,Chanda,Chandan,Chandavarkar,Chandola,Chandra,Chandran,Chandrasekhar,Chandrasekharan,Chandy,Char,Charan,Chari,Chatterji,Chattopadhyay,Chaturvedi,Chaudhari,Chaudhuri,Chawla,Chellappa,Cherian,Chettiar,Chetty,Chhabra,Chhajed,Chhikara,Chidambaram,Chikkamath,Chinnaswamy,Chitale,Chitnis,Chitransh,Chitty,Chockalingam,Chopra,Choudhary,Choudhury,Chouhan,Chowdhury,Chugh,Chukkapalli,Dabholkar,Dada,Daga,Dagar,Daha,Dahiya,Dalmia,Damle,Damodaran,Dandekar,Dangi,Dani,Daniel,Dara,Daruwala,Dasari,Dasgupta,Dash,Dastur,Date,Datta,David,Dawar,Dayal,De,Deb,Deep,Deo,Deol,Deshmane,Dev,Deva,Devan,Devanand,Devar,Devaraj,Devarajan,Devi,Dewan,Dey,Dhaliwal,Dhall,Dhami,Dhar,Dharia,Dharma,Dharmadhikari,Dhawan,Dhil,Dhillon,Dhingra,Dholakia,Dhoni,Dias,Dikshit,Dileep,Dinshaw,Divekar,Dodiya,Dogra,Doley,Dolia,Dongre,Dosanjh,Dravid,Dua,Dube,Dugar,Duggal,Dutt,Dwivedi,Eapen,Easwaran,Edwin,Elias,Emanuel,Engineer,Eswaran,Eswari,Farid,Farooqi,Farooqui,Faruqi,Fazal,Felix,Fernandes,Fernandez,Francis,Gabriel,Gadkari,Gadre,Gajjar,Gandhi,Ganesan,Ganesh,Gangadharan,Ganguly,Garo,Garrick,Gaur,Gautam,Gavaskar,Gawande,Gayakwad,George,Ghai,Ghatak,Ghorpade,Ghosh,Ghoshal,Gidwani,Gill,Giridhar,Giridharan,Gnanadesikan,Goda,Godrej,Goel,Gogoi,Gola,Gole,Golla,Gomes,Gomez,Gondi,Gopalan,Gopinath,Gor,Goradia,Goswami,Goud,Gouda,Gounder,Gowda,Gowtham,Goyal,Grewal,Grover,Guha,Gulati,Gunjal,Gupte,Gurbaxani,Guria,Guruswamy');
        
        $randomFirstName = $firstNames[array_rand($firstNames)];
        $randomLastName = $lastNames[array_rand($lastNames)];
        $fullName = $randomFirstName . " " . $randomLastName;
        $randomEmail = strtolower(str_replace(' ', '', $fullName) . rand(100, 9999) . "@gmail.com");

        $initHeaders = [
            "Host: www.ujalahappiestonam.com",
            "Content-Type: application/json",
            "Cookie: " . $cookie,
            "User-Agent: " . $userAgent
        ];
        $initRes = sendRequest($baseUrl . "/api/users", json_encode(["masterKey" => $masterKey]), $initHeaders);
        
        $initData = decryptResponse($initRes['response']['resp'] ?? '');
        $userKey = $initData['userKey'] ?? '';
        $dataKey = $initData['dataKey'] ?? '';

        if (!$userKey || !$dataKey) {
            echo json_encode(['status' => 'error', 'msg' => 'Reward Already Claimed']);
            exit;
        }

        $_SESSION['current_user_key'] = $userKey;
        $_SESSION['current_data_key'] = $dataKey;

        $imagePath = __DIR__ . '/image.jpg';
        if (!file_exists($imagePath)) {
            echo json_encode(['status' => 'error', 'msg' => 'Reward Already Claimed']);
            exit;
        }

        $payloadData = [
            "name" => $fullName,
            "mobile" => $mobile,
            "email" => $randomEmail,
            "city" => "Kerala", 
            "code" => "8902102126232", 
            "agreed1" => "Yes",
            "agreed2" => "Yes"
        ];
        
        $signedData = generateSignedData($payloadData, $userKey, $dataKey);

        $postFields = [
            'userKey' => $userKey,
            'pack' => new CURLFile($imagePath, mime_content_type($imagePath), 'image.jpg'),
            'data' => $signedData['signedString']
        ];

        $uploadHeaders = [
            "Host: www.ujalahappiestonam.com",
            "Authorization: Bearer ",
            "Cookie: " . $cookie,
            "User-Agent: " . $userAgent,
            "Origin: " . $baseUrl,
            "X-Requested-With: mark.via.gp"
        ];

        $res = sendRequest($baseUrl . "/api/users/getOTP/" . $userKey . "?t=" . $signedData['t'], $postFields, $uploadHeaders, true);
        
        if ($res['code'] !== 200) {
            echo json_encode(['status' => 'error', 'msg' => 'Reward Already Claimed']);
            exit;
        }

        echo json_encode(['status' => 'success', 'msg' => 'OTP Sent successfully!', 'data' => $res]);
        exit;
    }

    if ($action === 'verify_otp') {
        $userKey = $_SESSION['current_user_key'] ?? '';
        $dataKey = $_SESSION['current_data_key'] ?? '';
        $otp = trim($_POST['otp'] ?? '');

        if (!$userKey || !$dataKey) {
            echo json_encode(['status' => 'error', 'msg' => 'Session expired. Please reload.']);
            exit;
        }
        
        if (empty($otp)) {
            echo json_encode(['status' => 'error', 'msg' => 'Please enter the OTP.']);
            exit;
        }
        
        $payloadData = [ "otp" => $otp ];
        $signedData = generateSignedData($payloadData, $userKey, $dataKey);

        $postData = "userKey=" . $userKey . "&data=" . urlencode($signedData['signedString']);

        $verifyHeaders = [
            "Host: www.ujalahappiestonam.com",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: " . $cookie,
            "User-Agent: " . $userAgent,
            "Origin: " . $baseUrl,
            "X-Requested-With: mark.via.gp"
        ];

        $res = sendRequest($baseUrl . "/api/users/verifyOTP/" . $userKey . "?t=" . $signedData['t'], $postData, $verifyHeaders);
        
        $verifiedData = decryptResponse($res['response']['resp'] ?? '');
        
        if ($res['code'] === 200 && !empty($verifiedData)) {
            $token = $verifiedData['accessToken'] ?? $verifiedData['token'] ?? '';
            $_SESSION['jwt_token'] = $token;
            echo json_encode(['status' => 'success', 'msg' => 'OTP Verified Successfully!']);
        } else {
            $err = $verifiedData['error'] ?? $verifiedData['message'] ?? 'Invalid or Expired OTP.';
            echo json_encode(['status' => 'error', 'msg' => $err]);
        }
        exit;
    }

    if ($action === 'spin_and_claim') {
        $userKey = $_SESSION['current_user_key'] ?? '';
        $dataKey = $_SESSION['current_data_key'] ?? '';
        $token = $_SESSION['jwt_token'] ?? '';
        
        $authHeaders = [
            "Host: www.ujalahappiestonam.com",
            "Authorization: Bearer " . $token,
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie: " . $cookie,
            "User-Agent: " . $userAgent,
            "Origin: " . $baseUrl,
            "X-Requested-With: mark.via.gp"
        ];

        $spinSigned = generateSignedData([], $userKey, $dataKey);
        $spinPost = "userKey=" . $userKey . "&data=" . urlencode($spinSigned['signedString']);
        $spinRes = sendRequest($baseUrl . "/api/users/speenTheWheel/" . $userKey . "?t=" . $spinSigned['t'], $spinPost, $authHeaders);
        $spinDecoded = decryptResponse($spinRes['response']['resp'] ?? '');

        $claimSigned = generateSignedData([], $userKey, $dataKey);
        $claimPost = "userKey=" . $userKey . "&data=" . urlencode($claimSigned['signedString']);
        $claimRes = sendRequest($baseUrl . "/api/users/claimNow/" . $userKey . "?t=" . $claimSigned['t'], $claimPost, $authHeaders);
        $claimDecoded = decryptResponse($claimRes['response']['resp'] ?? '');

        echo json_encode([
            'status' => 'success', 
            'msg' => 'Reward Claimed!', 
            'spin_data' => $spinDecoded,
            'claim_data' => $claimDecoded
        ]);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>SpeedX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg: #f7f7f8; --white: #ffffff; --border: #e5e5ea; --ink: #111118; --ink2: #6c6c80; --ink3: #b0b0c0; --blue: #2563eb; --blue-bg: #eff3ff; --green: #15803d; --green-bg: #f0fdf4; --green-border: #bbf7d0; --red: #b91c1c; --red-bg: #fef2f2; --red-border: #fecaca; --f: 'Inter', sans-serif; --r: 12px; }
        body { background: var(--bg); color: var(--ink); font-family: var(--f); min-height: 100vh; padding-bottom: 60px; font-size: 14px; line-height: 1.5; -webkit-font-smoothing: antialiased; }
        .wrap { max-width: 420px; margin: 0 auto; padding: 0 16px; }
        .hdr { display: flex; align-items: center; justify-content: center; padding: 20px 0 18px; border-bottom: 1px solid var(--border); margin-bottom: 24px; }
        .logo { font-size: 20px; font-weight: 700; letter-spacing: -.3px; }
        .logo i { color: var(--blue); margin-right: 6px; }
        .logo span { color: var(--blue); }
        .fcard { background: var(--white); border: 1px solid var(--border); border-radius: var(--r); padding: 20px 16px; margin-bottom: 12px; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .fd { margin-bottom: 15px; }
        .fd label { display: block; font-size: 12px; font-weight: 600; color: var(--ink2); margin-bottom: 6px; }
        .inp { width: 100%; background: var(--bg); border: 1px solid var(--border); color: var(--ink); border-radius: 8px; padding: 12px; font-size: 14px; font-family: var(--f); outline: none; transition: border-color .15s, box-shadow .15s; }
        .inp:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(37, 99, 235, .08); }
        .btn-row { display: flex; gap: 10px; margin-top: 18px; }
        .btn { padding: 12px 14px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; font-family: var(--f); transition: opacity .15s; display: inline-flex; align-items: center; justify-content: center; gap: 6px; width: 100%; }
        .btn-main { background: var(--blue); color: #fff; }
        .btn-main:hover { opacity: .88; }
        .btn:disabled { opacity: .5; cursor: not-allowed; }
        .alert-box { padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 13px; font-weight: 600; display: none; align-items: center; gap: 8px; }
        .alert-success { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .alert-error { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .success-box { text-align: center; padding: 30px 10px; }
        .success-title { font-size: 22px; font-weight: 700; color: var(--green); margin-bottom: 10px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .reward-info { margin-top: 15px; padding: 15px; background: var(--green-bg); border: 1px solid var(--green-border); border-radius: 8px; color: var(--green); font-weight: 600; font-size: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .foot { text-align: center; font-size: 13px; font-weight: 500; color: var(--ink3); padding-top: 30px; margin-top: 20px; }
    </style>
</head>
<body>

<div class="wrap">
    <div class="hdr">
        <div class="logo"><i class="fas fa-bolt"></i>Win ₹50 <span>Rewards</span></div>
    </div>

    <div class="fcard" id="step1">
        <div id="alert1" class="alert-box"></div>
        <form id="registerForm">
            <div class="fd">
                <label>Mobile Number</label>
                <input type="number" id="mobileInput" class="inp" placeholder="Enter Mobile Number" required>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-main" id="btnReg">
                    <i class="fas fa-paper-plane" id="regIcon"></i> Submit
                </button>
            </div>
        </form>
    </div>

    <div class="fcard" id="step2" style="display: none;">
        <div id="alert2" class="alert-box"></div>
        <form id="otpForm">
            <div class="fd">
                <label>Enter OTP</label>
                <input type="number" id="otpVal" class="inp" placeholder="Enter 6-digit OTP" required>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-main" id="btnOtp">
                    <i class="fas fa-check-circle" id="otpIcon"></i> Verify OTP 
                </button>
            </div>
        </form>
    </div>

    <div class="fcard" id="step4" style="display: none;">
        <div class="success-box">
            <div class="success-title">
                <i class="fas fa-trophy" style="font-size: 42px;"></i>
                Congratulations!
            </div>
            <p style="color: var(--ink2);">Your reward has been claimed successfully.</p>
            <div id="rewardBox" class="reward-info" style="display:none;"></div>
        </div>
    </div>

    <div class="foot">Created By SpeedX</div>
</div>

<script>
    function showAlert(step, msg, isError = false) {
        const alertBox = document.getElementById('alert' + step);
        if (!alertBox) return;
        alertBox.style.display = 'flex';
        alertBox.className = 'alert-box ' + (isError ? 'alert-error' : 'alert-success');
        let icon = isError ? '<i class="fas fa-exclamation-circle"></i>' : '<i class="fas fa-check-circle"></i>';
        alertBox.innerHTML = icon + ' <span>' + msg + '</span>';
    }

    function hideAlert(step) {
        const alertBox = document.getElementById('alert' + step);
        if (alertBox) alertBox.style.display = 'none';
    }

    function toggleBtn(btnId, iconId, isLoading, originalClass) {
        const btn = document.getElementById(btnId);
        const icon = document.getElementById(iconId);
        btn.disabled = isLoading;
        icon.className = isLoading ? 'fas fa-spinner fa-spin' : originalClass;
    }

    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toggleBtn('btnReg', 'regIcon', true, 'fas fa-paper-plane');
        hideAlert('1');

        let formData = new FormData();
        formData.append('action', 'register');
        formData.append('mobile', document.getElementById('mobileInput').value);

        try {
            let res = await fetch('', { method: 'POST', body: formData });
            let data = await res.json();
            
            if (data.status === 'success') {
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
                showAlert('2', 'OTP Sent successfully!');
            } else {
                showAlert('1', 'Reward Already Claimed', true);
            }
        } catch (err) {
            showAlert('1', 'Reward Already Claimed', true);
        }
        toggleBtn('btnReg', 'regIcon', false, 'fas fa-paper-plane');
    });

    document.getElementById('otpForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        toggleBtn('btnOtp', 'otpIcon', true, 'fas fa-check-circle');
        hideAlert('2');

        let formData = new FormData();
        formData.append('action', 'verify_otp');
        formData.append('otp', document.getElementById('otpVal').value);

        try {
            let res = await fetch('', { method: 'POST', body: formData });
            let data = await res.json();
            
            if (data.status === 'success') {
                showAlert('2', 'OTP Verified! Claiming reward...');
                
                let spinData = new FormData();
                spinData.append('action', 'spin_and_claim');
                
                try {
                    let sRes = await fetch('', { method: 'POST', body: spinData });
                    let sData = await sRes.json();
                    
                    if (sData.status === 'success') {
                        document.getElementById('step2').style.display = 'none';
                        document.getElementById('step4').style.display = 'block';
                        
                        let sObj = sData.spin_data || {};
                        let cObj = sData.claim_data || {};
                        let rewardText = sObj.reward || sObj.amount || sObj.prize || sObj.rewardType || cObj.reward || cObj.msg;
                        
                        if (rewardText) {
                            let rBox = document.getElementById('rewardBox');
                            rBox.innerHTML = '<i class="fas fa-gift"></i> You Won: ' + rewardText;
                            rBox.style.display = 'flex';
                        }
                    } else {
                        showAlert('2', 'Failed to claim reward.', true);
                        toggleBtn('btnOtp', 'otpIcon', false, 'fas fa-check-circle');
                    }
                } catch (err) {
                    showAlert('2', 'Network error while claiming.', true);
                    toggleBtn('btnOtp', 'otpIcon', false, 'fas fa-check-circle');
                }

            } else {
                showAlert('2', data.msg, true);
                toggleBtn('btnOtp', 'otpIcon', false, 'fas fa-check-circle');
            }
        } catch (err) {
            showAlert('2', 'Request failed.', true);
            toggleBtn('btnOtp', 'otpIcon', false, 'fas fa-check-circle');
        }
    });
</script>

</body>
</html>
