<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configure extends FHCOP_Controller
{


    public function index()
    {
        $res = $this->rest->get('types');
        $this->load->helper("url");

        $json_file = file_get_contents(getcwd() . "/application/config/openproject.json");

        $json_data = json_decode($json_file, true);


        $curl=curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://fhcop-inf.technikum-wien.at/openproject/api/v3/types/");
        curl_setopt($curl, CURLOPT_USERPWD,
        'apikey:4b3de1532078925f0f6c652feeddae25659cd30b'
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $curldata=curl_exec($curl);
        $curljson=json_decode($curldata,TRUE);
        $wpTypeArray=array();
        foreach ($curljson['_embedded']['elements']as $key=> $value){
            $wpTypeArray[$value['_links']['self']['title']]=$value['_links']['self']['href'];
        }
        $wpTypes=array_keys($wpTypeArray);
        $options1="";
        $options2="";
        $options3="";
        $options4="";
        foreach (array_keys($wpTypeArray) as $value){
            $options1=$options1.'<option'.($json_data['type_mapping']['Arbeitspaket']['title']==$value?' selected="selected"':'').'>' .$value.'</option>';
            $options2=$options2.'<option'.($json_data['type_mapping']['Milestone']['title']==$value?' selected="selected"':'').'>' .$value.'</option>';
            $options3=$options3.'<option'.($json_data['type_mapping']['Task']['title']==$value?' selected="selected"':'').'>' .$value.'</option>';
            $options4=$options4.'<option'.($json_data['type_mapping']['Projektphase']['title']==$value?' selected="selected"':'').'>' .$value.'</option>';
        }
$alert="";
        curl_close($curl);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            foreach ($_POST as $key => $value) {
                switch ($key) {
                    case 'url':
                        $json_data["server"]=$value;
                        break;
                    case 'api_key':
                        $json_data["api_key"]=$value;
                        break;
                    case 'api_path':
                        $json_data["api_path"]=$value;
                        break;
                    case 'workpackage':
                        if(in_array($value,$wpTypes)) {
                            $json_data['type_mapping']['Arbeitspaket']['title'] = $value;
                            $json_data['type_mapping']['Arbeitspaket']['href'] = $wpTypeArray[$value];
                        }
                            break;
                    case 'milestone':
                        if(in_array($value,$wpTypes)) {
                            $json_data['type_mapping']['Milestone']['title'] = $value;
                            $json_data['type_mapping']['Milestone']['href'] = $wpTypeArray[$value];
                        }
                        break;
                    case 'task':
                        if(in_array($value,$wpTypes)) {
                            $json_data['type_mapping']['Task']['title'] = $value;
                            $json_data['type_mapping']['Task']['href'] = $wpTypeArray[$value];
                        }
                        break;
                    case 'projectphase':
                        if(in_array($value,$wpTypes)) {
                            $json_data['type_mapping']['Projektphase']['title'] = $value;
                            $json_data['type_mapping']['Projektphase']['href'] = $wpTypeArray[$value];
                        }
                        break;
                }

            }

            file_put_contents(getcwd() . "/application/config/openproject.json", json_encode($json_data,JSON_UNESCAPED_SLASHES),LOCK_EX);
            $alert='<div class="alert alert-success"> <strong>Success!</strong> Your settings are saved, please refresh this page to see the changes!.</div>';
        }
        $this->load->library('parser');
        $data = array(
            'url' => $json_data["server"],
            'api_key' => $json_data["api_key"],
            'api_path' => $json_data["api_path"],
            'options1' => $options1,
            'options2' => $options2,
            'options3' => $options3,
            'options4' => $options4,
            'alert' => $alert
        );
        $this->parser->parse('configure', $data);

        }


}