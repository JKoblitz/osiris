<?php

class IDA
{
    private $authorized = false;
    public $msg = '';
    private $token = null;
    private $mail = null;
    public $institute_id = null;
    public $institutes = [];
    public $dataset_id = null;
    private $base_url = "https://mainly.api.ida.leibniz-gemeinschaft.de";

    public $user = [];
    public $last_title = '';
    public $year = 2020;
    public $state = '';

    function __construct($email = null, $password = null)
    {
        if ($email !== null && $password !== null) {
            $msg = $this->auth($email, $password);
            if ($msg !== true) $this->msg = $msg;
            else $this->authorized = true;
        } else if (isset($_SESSION['ida-token']) && isset($_SESSION['ida-mail'])) {
            $this->token = $_SESSION['ida-token'];
            $this->mail = $_SESSION['ida-mail'];
            $this->authorized = true;
            $this->institute_id = $_SESSION['ida-institute_id'];
            $this->institutes = $_SESSION["ida-institutes"];
        } else {
            $this->msg = 'Unauthorized';
        }

        if (!isset($_SESSION["ida-year"])) {
            // Year-Suggestion: previous year (jan-nov) or current year (in dec)
            $_SESSION["ida-year"] = (date("n") > 11 ? date("Y") : date("Y") - 1);
        }

        $this->year = $_SESSION["ida-year"];


        //         $this->base_url."/incoming/formulars";
        // $this->base_url."/incoming/formulars/".$formular_url."/applications";
        // $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/applications?formular_ids=".$formular_id;
        // $this->base_url."/incoming/formulars/".$formular_id."/custom_fields";
        // $this->base_url."/incoming/formulars/".$formular_id."/applications/".$dataset_id."/steps";
        // $this->base_url."/incoming/formulars/".$formular_id."/step_tiles";
        // $this->base_url."/auth/sign_in";
        // // $this->base_url."/incoming/dashboard_items/".$dashboard_item_id; // request a given dashboard
        // // $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/dashboard_items"; // request dashboard items for the institution
        // $this->base_url."/incoming/institutions/".$_SESSION["ida_institute_id"]."/applications";
        // $this->base_url."/incoming/applications/".$dataset_id."/custom_field_values";
        // $this->base_url."/incoming/formulars/".$formular_url."/applications/".$dataset_slug."/previous";
        // $this->base_url."/incoming/applications/".$previous_dataset_id."/custom_field_values";
        // $this->base_url."/incoming/applications/".$dataset_id."/custom_field_values',".$custom_fields[$attribute_name]["id"].",'".$gris_value[0]."','".$_SESSION["ida_email"]."','".$_SESSION["ida_authentication_token"]."')", "ida_dataset_".$dataset_id." ida_step_".$custom_fields[$attribute_name]["step_id"]);
        // $this->base_url."/incoming/formulars/".$formular_id."/applications/".$dataset_id."/steps/".$step_id;
    }

    function is_authorized()
    {
        return $this->authorized;
    }

    function auth($email, $password)
    {
        $this->authorized = false;
        $data = json_encode([
            "user" => [
                "email" => $email,
                "password" => $password
            ]
        ]);
        $url = "/auth/sign_in";
        $result = $this->request($url, 'POST', $data);

        if (isset($result['error'])) return ($result['error']);
        if (!isset($result['authentication_token'])) return $result;

        $this->token = $result['authentication_token'];
        $this->mail = $result['email'];
        $this->user = $result;
        $this->institutes = $result["institutions"];
        $this->institute_id = $result["institutions"][0]["id"];
        $this->authorized = true;

        $_SESSION['ida-token'] = $this->token;
        $_SESSION['ida-mail'] = $this->mail;
        $_SESSION['ida-institute_id'] = $this->institute_id;
        $_SESSION['ida-institutes'] = $this->institutes;
        return true;
    }

    function check($result)
    {
        if (isset($result['error'])) {
            $this->authorized = false;
            $this->msg = $result['error'];
            return false;
        }
        return true;
    }

    function dashboard()
    {

        $url = "/incoming/institutions/" . $this->institute_id . "/dashboard_items";
        $result = $this->request($url);

        return $result[0] ?? false;
    }
    function formular($id)
    {
        $result = [];
        $url = "/incoming/formulars/$id/custom_fields";
        $result['custom_fields'] = $this->request($url, 'GET');

        $url = "/incoming/formulars/$id/step_tiles";
        $result['step_tiles'] = $this->request($url, 'GET');

        $url = "/incoming/institutions/" . $this->institute_id . "/applications?formular_ids=" . $id;
        $result['applications'] = $this->request($url, 'GET');

        if (empty($result['applications'])) {
            // no dataset available?
            $this->msg = "Fehler: Das Formular muss in IDA mindestens einmal geöffnet werden, da sonst keine Daten vorhanden sind.";
            return [];
        }
        $app = $result['applications'][0];
        $this->dataset_id = $app["id"];
        $dataset_slug = $app["slug"];
        $formular_url = $app["formular_url"];
        $this->state = $app["state"];


        $url = "/incoming/formulars/" . $id . "/applications/" . $this->dataset_id . "/steps";
        $result['steps'] = $this->request($url, 'GET');

        $url = "/incoming/applications/" . $this->dataset_id . "/custom_field_values";
        $current = $this->request($url, 'GET');
        $result['values'] = array_column($current, "value", "custom_field_name");

        $url = "/incoming/formulars/" . $formular_url . "/applications/" . $dataset_slug . "/previous";
        $previous = $this->request($url, 'GET');

        if (empty($previous) || !isset($previous[0]["id"])) {
            // Indicate non-existing previous datasets
            $result['previous_values'] = [];
        } else {
            $previous_dataset_id = $previous[0]["id"];
            // $previous_dataset_slug = $previous["slug"];
            $url = "/incoming/applications/" . $previous_dataset_id . "/custom_field_values";
            $previous = $this->request($url, 'GET');
            if (!empty($previous))
                $result['previous_values'] = array_column($previous, "value", "custom_field_name");
        }

        // dump( $result['values']);
        // dump( $result['previous_values']);
        // die;

        return $result;
    }

    function fields($id)
    {
        // $uri = 'formular17_2023';
        $url = "/incoming/formulars/$id/custom_fields";
        // $data = ['ids'=>$id];
        $result = $this->request($url, 'GET');

        if (isset($result['error'])) return ($result['error']);

        return $result;
    }

    /**
     * Find the label of an attribute
     *
     * @param array 	$field   Custom field data
     * @param array 	$tile      Step tile data
     * @param array  	$step           Optional step data
     *	 
     * @return array    Returns the possible label of the attribute
     */
    function label($field, $tile = null)
    {
        $label = "";
        $labels = array();
        $caption = "";

        if ($tile === null) {

            if ($field['typecast'] == 'typecheckbox') {
                $labels[] = $field['checkbox_label']['de'];
            }
        } else if ($tile["tile_type"] === "tabletile") {
            $cell_index = array_search($field["id"], $tile["tablegrid"]["cells"]);
            $row_index = intval($cell_index / ($tile["cols"] - 1));
            $header_index = $cell_index % ($tile["cols"] - 1);
            $row = $tile["tablegrid"]["rows"][$row_index];
            $header = $tile["tablegrid"]["headers"][$header_index];
            if (!empty($row["de"]) || !empty($header["de"])) $labels[] = $row["de"] . " <i>(" . $header["de"] . ")</i>";
            if (array_key_exists("de", $field["description"]) && !empty(($field["description"]["de"]))) $labels[] = strip_tags($field["description"]["de"]);
            if (array_key_exists("de", $tile["title"]) && !empty(($tile["title"]["de"]))) $caption = strip_tags($tile["title"]["de"]);
            else if (array_key_exists("de", $tile["description"]) && !empty(($tile["description"]["de"]))) $caption = strip_tags($tile["description"]["de"]);
        } else if ($tile["tile_type"] === "fieldstile") {
            if (!empty(($field["title"]["de"]))) $labels[] = strip_tags($field["title"]["de"]);
            if (!empty(($field["description"]["de"]))) $labels[] = strip_tags($field["description"]["de"]);
            if (array_key_exists("de", $tile["description"]) && !empty(($tile["description"]["de"]))) $labels[] = strip_tags($tile["description"]["de"]);
            if (array_key_exists("checkbox_label", $field) && !empty(($field["checkbox_label"]["de"]))) $labels[] = strip_tags($field["checkbox_label"]["de"]);
            $caption = array_key_exists("de", $tile["title"]) ? ($tile["title"]["de"]) : "";
        }
        if (!empty($caption))
            $caption = strip_tags($caption);
        if ($this->last_title == $caption) {
            $caption = "";
        } else {
            $this->last_title = $caption;
        }

        if (count($labels) == 0) {
            $label = $caption;
        } else {
            if ($caption !== "") $label = "<b>" . $caption . "</b></br>";
            $label .= $labels[0];
        }
        if (count($labels) > 1) {
            $l = array_filter(array_slice($labels, 1));
            if (!empty($l))
                $label .= " <i>[" . implode(" -- ", $l) . "]</i>";
        }

        return $label;
    }


    function request($url, $method = 'GET', $data = null)
    {
        $curl = curl_init();

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            if (!empty($data))
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            if (!empty($data))
                $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        $header = [
            'accept: application/json',
            'Content-Type: application/json'
        ];
        if (!empty($this->token) && !empty($this->mail)) {
            $header[] = "X-USER-EMAIL: " . $this->mail;
            $header[] = "X-USER-TOKEN: " . $this->token;
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $this->base_url . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if ($result === false) {
            dump(curl_error($curl));
            throw new Exception(curl_error($curl), curl_errno($curl));
        }
        curl_close($curl);

        $result = json_decode($result, true);
        $this->check($result);

        return $result;
    }
}
