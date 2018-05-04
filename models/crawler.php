<?php
require_once($_SERVER['DOCUMENT_ROOT']."/GeoOdessaApp1/models/establishment.php");

/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.01.2018
 * Time: 22:32
 */
    //-----------------------------------
    //створення об'єкту Parser
    //$parser1 = new Parser();
    //перевірка дати
    //парсер вмикається, якщо настав 1-й день місяця
    //$parser1->check_date();
    //$parser1->run_parser();

    ini_set('max_execution_time', 300);
    //клас для управлінням парсерм
    class Parser
    {


        public static $time_start     = 0;
        public static $time_end      = 0;
        /**
         * Parser constructor.
         */
        //пустий конструктор потрібний для доступу до методів класу
        public function __construct()
        {
        }

        //private модифікатор дозволяє доступ до функції лише із даного класу
        //
        private function get_full_poi_info($url)
        {
            $testing = array();

            $xpath = $this->open_poi_site($url);

            $site_nodes = $xpath->query('//table[@class="guide_search_result"]/tbody//span');

            //helps to check amount of establishments in the page
            $site_nodes_size = $xpath->query("//table[@class='guide_search_result']/tbody//td[@class='nmr'][position()=last()]");
            $size = count($site_nodes_size);

            array_push($testing,  ['page_pois_amount' => $size]);

            $switcher = false;
            //$switcher2 = false;

            foreach ($site_nodes as $node) {
                $span_class = $node->getAttribute('class');
                $xpath2 = null;
                if ($span_class == "bhead") {

                    $name = $this->get_poi_name($node);
                    $url = $this->get_poi_osvita_addr($xpath, $node);

                    $xpath2 = $this->open_poi_site($url);
                    $description = $this->get_poi_description($xpath2);

                    $load_table = $xpath2->query('//table[@class="topimg"]');

                    foreach ($load_table as $child_nodes) {
                        $poi_site_addr = $this->get_poi_site($xpath2, $child_nodes);
                        //$poi_site_addr = 'http://' . $poi_site_addr;

                        $phones = $this->get_poi_phones($xpath2, $child_nodes);

                        $cat_name = $this->get_poi_type($xpath2, $name);
                        $lowc_cat_name = mb_strtolower($cat_name, 'UTF-8');

                        $cat_id = establishment::get_category_id($lowc_cat_name);
                        if ($cat_id == "")
                            $cat_id = 8;

                    }
//------------------------------end---------------------------------------------------------------------------------

                } elseif ($span_class == "bdate") {

                    $address = $this->get_address($node);
                    $coordinates = $this->get_poi_coord($node);
                    //$coordinates = '';

                    $switcher = true;
                }

                if ($switcher == true) {
                    //user name = system
                    $user_id = 2;
                    $create_date = date('Y-m-d');
                    // $coordinates, $name, $cat_id, $description, $url, $photo, $email, $directions,
                    //                    $addresses, $phones, $user_id, $create_date
                    //$result = establishment::AddPoi($coordinates, $name, '', '', '', $description, '', '', '', '', '');
                    try {
                        $result = establishment::AddUncheckPoi($coordinates, $name, $cat_id, $description, $poi_site_addr, '', '', '', $address, $phones, $user_id, '');
                    } catch(Exception $e) {
                        //echo $e->getTraceAsString();
                    }

                    array_push($testing, [$name => $result]);

                    $switcher = false;
                }
            }
            //memory_get_peak_usage();
            //$xpath = null;

            return $testing;
        }

        private function open_poi_site($url)
        {
            $dom = new DOMDocument('1.0', 'UTF-8');
            @$dom->loadHTMLFile(htmlspecialchars($url));
            $xpath = new DOMXPath($dom);

            return $xpath;
        }

        //функція, яка запускає роботу парсера
        public static function run_parser()
        {
            $urls = array();
            //перша адреса для аналізу
            //дана сторінка містить ВНЗ Одеси + Одеської області
            $cur_url = 'http://osvita.ua/vnz/college/search-41-122-0-0-32-0.html';
            $cur_url2 = 'https://osvita.ua/vnz/guide/search-17-0-63-0-0.html';
            //для зручності адресу поміщено до наступної змінної
            $url1 = $cur_url;
            $url2 = $cur_url2;

            array_push($urls, $url1);
            array_push($urls, $url2);

            $testing = array();

            foreach ($urls as $url) {
                //для роботи із сайтом створено об'єкт класу DOMXPath
                //сайт поміщено у XML об'єкт
                //щоб працювати з об'єктом використовується об'єкт класу DOMXPath
                $xpath = (new self)->open_poi_site($url);

                //виконано запит для отримання всіх сторінок із Одеськими ВНЗ
                $url_arr = $xpath->query('//div[@class="paginator"]//a/@href');
                //кількість сторінок
                $url_amount = count($url_arr);
                //індекс за допомогою якого здійснюється прохід по всім сторінкам
                //індекс поточної сторінки
                $url_cur = 0;

                while ($url_cur < $url_amount) {
                    $testing_cur = (new self)->get_full_poi_info($url);
                    foreach ($testing_cur as $item)
                    {
                        array_push($testing, $item);
                    }
                    //$testing = $testing_cur;
                    //рух індексу для отримання наступної сторінки
                    $url_cur += 1;
                    //береться поточна сторінка
                    //присвоюється для аналізу в наступній ітерації циклу
                    $url = $url_arr[$url_cur];
                    //якщо неправильна адреса поточної сторінки
                    //або адреси не існує, то виконується наступна ітерація

                    if ($url != '') {
                       $page = trim($url->nodeValue);
                    } else
                       break;

                    $url = 'https://osvita.ua' . $page;

                }

                $xpath = null;
            }

            return $testing;
        }

        /* gets the data from a URL */
        private function get_data($url) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }

        //функція отримання координат
        private function get_сoords_by_addr($addr)
        {
            $key = "953c5128a60dd9";

            $addr_e = urlencode($addr);
            $key_e = urlencode($key);
            //$addr = "Одеса Авіаційна 18";

            sleep(1);
            //$url = "https://eu1.locationiq.org/v1/search.php?key=" . $key_e . "&q=" . $addr_e . "&format=json";
            $url = "https://api.opencagedata.com/geocode/v1/json?q=". $addr_e ."&key=b5f83837534d4716b8b28297f79225f7";

            //$url = "https://nominatim.openstreetmap.org/search.php?q=" . $addr_e . "&format=json&addressdetails=1&limit=1";
            $coordinates = '';

            $json = @file_get_contents($url);
                if ($json == '[]' || $json == null)
                    return $coordinates;
            else {
                $obj = json_decode($json);
                //$obj->fetch_array();

                //$lat = $obj->results[0]->geometry;

                $lat = $obj->results[0]->geometry->lat;
                //$lat = $obj[0]->lat;
                //$lon = $obj[0]->lon;
                $lon = $obj->results[0]->geometry->lng;
                $array = array($lat, $lon);
                $coordinates = implode(",", $array);

                return $coordinates;
            }

        }//end of function

        private function get_poi_name($item)
        {
            $name = $item->nodeValue;

            return $name;
        }

        //функція отримання адреси точки на сайті osvita
        private function get_poi_osvita_addr($xpath, $item)
        {
            //отримання всіх адрес тегу: table[@class="guide_search_result"]/tbody//span - тобто тегу span
            $url_parts_arr = $xpath->query('a', $item);
            //отримання адреси точки на сайті
            $url_part = $url_parts_arr[0]->getAttribute('href');
            $full_url = 'https://osvita.ua' . $url_part;

            return $full_url;

        }

        //функція отримання веб-адреси сайту закладу
        private function get_poi_site($xpath, $child_tags)
        {
            //отримання посилань
            $site_tag = $xpath->query('tr/td/a', $child_tags);
            //отримання адреси із непотрібними атрибутами
            $site_trash = $site_tag[0]->getAttribute('href');
            //образіння початку строки до символів включно '//'
            //потрыбно для прибирання зазвичай 'https://'
            $dirty_addr = explode('//', $site_trash);
            //заміна зайвого символу адреси в кінці
            $site = '';
            if (isset($dirty_addr[1]))
                $site = str_replace("/", "", $dirty_addr[1]);

            return $site;
        }

        //функцыя отримання телефоныв
        private function get_poi_phones($xpath, $child_tags)
        {
            $tag_phone = $xpath->query('tr/td[@class="vmiddle"]', $child_tags);
            //отримання строчки із телефонами
            $tag_phone_val = $tag_phone[0]->nodeValue;
            //обрізання перших 4 символів "\n\t..."
            $cut_begin_trash = substr($tag_phone_val, 4);
            //розділення підстроки по символу табуляції
            //і розміщення кожної частини в масиві
            $cut_end_trash = explode("\t", $cut_begin_trash);
            //отримання підстроки із номерами до табуляції
            $numbers_str = $cut_end_trash[0];

            //виявлення ",", які синалізуюють про наявність більше, ніж 1-го номера
            //в строчці
            $number_arr = explode(",", $numbers_str);
            //якщо масив було розділено за сиволом "," - то номерів більше 1-го,
            // кожен поміщений в окрему комірку масиву, після цього
            if (sizeof($number_arr) == 1) {

                //патерн - число та пробіл
                //за патерном здійснюється наступний пошук номерів
                //номер може закінчуватись на число потім ставиться пробіл
                //після починається новий
                $pattern = '/[0-9]\\s/';

                //поки що знахідок не було, тому змінна = 0
                $is_matched = 0;
                //шукаю номери за патерном
                preg_match($pattern, $numbers_str, $is_matched, PREG_OFFSET_CAPTURE);
                //при виявленні співпалінь частини номеру із патерном
                if (sizeof($is_matched) > 0) {
                    //
                    $start_to_split = 0;
                    for ($i = 0; $i < sizeof($is_matched); $i++) {
                        $position_to_split = $is_matched[$i][1];
                        try {
                            //очищується змінна
                            unset($number_arr);
                            //індекс, де виявлено співпадіння вказує на число
                            //тоді необхідно до індексу додати пробіл
                            //а наступний номер починатиметься після пробілу, тому
                            //додається ще одна позиція
                            $position_to_split += 2;
                            //отримання першого номеру
                            $number_arr[] = substr($numbers_str, $start_to_split, $position_to_split);
                            //отримання другого
                            $number_arr[] = substr($numbers_str, $position_to_split);

                            $start_to_split = $position_to_split;

                        } catch (Exception $ex) {

                        }
                    }


                }
            }

            return $number_arr;
        }

        //функція отримання категорії
        private function get_poi_type($xpath, $name)
        {
            $type_tag = $xpath->query('//table[@class="w620"]/tr/td[2]');
            $type = "";
            if ($type_tag[0] == "")
            {
                $db_connection = pg_connect("host=localhost port=5432 dbname=geoAppDb user=postgres password=1111");
                $select_query = "select * from categories";
                $result = pg_query($db_connection, $select_query);
                //$row = pg_fetch_row($result);
                while ($row = pg_fetch_row($result))
                {
                    if (strpos($name, $row[1]) !== false) {
                        //echo 'true';
                        $type = $row[1];
                        return $type;
                    }
                }


            } else $type = $type_tag[0]->nodeValue;

            return $type;
        }

        //функція перевірки строки на спец символи
        function isValid($str) {
            return !preg_match('/[^A-Za-z0-9.#\\-$]/', $str);
        }

        //функція отримання опису закладу
        private function get_poi_description($xpath)
        {
            $description_tag = $xpath->query('//div[@id="desc_ext"]');
			
			$paragraphs = '';
                //$des = $description_tag->nodeValue;
                foreach ($description_tag as $p_tag) {
                    $p = "<p>" . $p_tag->nodeValue;
                    $paragraphs .= $p;
                }
                $descr_cut_end = $paragraphs;
			
            $description_trash = $description_tag[0]->nodeValue;
            $descr_cut_begin = explode("Загальна інформація", $description_trash);
            if (isset($descr_cut_begin[1])) {

                $descr_cut_end = $descr_cut_begin[1];

                if ($this->isValid($descr_cut_end)) {
                    $description_arr = explode('.', $descr_cut_end);
                    $description = $description_arr[0];
                };
            } else {
                $descr_tag = $xpath->query('//div[@id="description"]//p//text() | //div[@id="description"]//ul/li/text()');
                $paragraphs = '';
                //$des = $description_tag->nodeValue;
                foreach ($descr_tag as $p_tag) {
                    $p = "<p>" . $p_tag->nodeValue;
                    $paragraphs .= $p;
                }
                $descr_cut_end = $paragraphs;
            }

            $description = $descr_cut_end;
            //addslashes($description);

            return $description;
        }

        private function get_address($node) {

            $address_poi = $node->nodeValue;
            $clear_comma = str_replace(",", "", $address_poi);

            if (strpos($clear_comma, 'область') !== false) {
                $split_addr = explode("область", $clear_comma);
                $address_poi_arr[] = $split_addr[1];
            } elseif (strpos($clear_comma, 'обл. ') !== false) {
                $split_addr = explode("обл. ", $clear_comma);
                $address_poi_arr[] = $split_addr[1];
            } else $address_poi_arr[] = $clear_comma;

            return $address_poi_arr;
        }

        //отримання координат за адресою
        private function get_poi_coord($node)
        {
            //отримання тегу із адресою
            $address_poi = $node->nodeValue;
            //очищення строчки із адресою
            $clear_comma = str_replace(",", "", $address_poi);
            $clear_city = str_replace("м.", "", $clear_comma);
            $clear_alley = str_replace("пров.", "", $clear_city);
            $clear_street = str_replace("вул.", "", $clear_alley);
            //адреса
            $clear_addr = $clear_street;

            //перевірка на заборонені словосполучення
            //при їх залишенні сервер не знаходить точки
            if (strpos($clear_addr, 'Одеська область') !== false) {
                $split_addr = explode("Одеська область", $clear_addr);
                $addr = $split_addr[1];
            } elseif (strpos($clear_addr, 'обл. ') !== false) {
                $split_addr = explode("обл. ", $clear_addr);
                $addr = $split_addr[1];
                //$addr = "Одеса " . $addr;
            } else $addr = $clear_addr;

            $coordinates = $this->get_сoords_by_addr($addr);

            return $coordinates;
        }

        public function check_date()
        {
            $cur_day = date("d") + 1;
            if ($cur_day == 1)
                run_parser();
        }

    }