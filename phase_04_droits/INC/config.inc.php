<?php

if (count(get_included_files()) == 1) die("You are not the access");

class config{
    private $filename ='config.ini.php';
    //private $filename ='autreConfig.ini';
    private $fileExist = false;
    private $config = [];
    private $saveError = 0;


    public function __construct($filename = null) {
        if ($filename != null) $this->filename = $filename;
        $this->fileExist = file_exists($this->filename);
    }

    function load($filename = null) {
        if ($filename != null) {
            if (!file_exists($filename)){
                return 'Le fichier demandé (' . $filename . ') n\'existe pas';}
            return $this->config = parse_ini_file($filename, true);
        } else {
            return $this->config = parse_ini_file($this->filename, true);
        }
    }

    function getForm() {
        $config = $this->getConfig();
        if (empty($this->config)) return $config;

        $out = [];
        $out[] = '<form action="formSubmit.html" id="sauveConfig" name="sauveConfig" method="post">';

        // Unset Error type
        unset($config['ERREUR']);
        unset($config['DB']);

        foreach ($config as $k => $v) {
            $out[] = '<fieldset><legend>' . $k . '</legend>';
            $out = array_merge($out, $this->getBloc($k, $v));
            $out[] = '</fieldset>';
        }

        $out[] = '<input type="submit" name="envoie" value="Envoyer"></form>';
        return implode($out, "\n");
    }

    // Function interne de getForm
    private function getBloc($k, $v) {

        /**
         * @var $min String
         * @var $max String
         * @var $pas String
         * @var $choix Array
         */

        $oKey = ['min', 'max', 'pas', 'choix'];

        foreach ($oKey as $key) {
            $$key = isset($v[$key]) ? $v[$key] : null;
            unset($v[$key]);
        }

        $out = [];

        foreach ($v as $item => $value) {
            $out[] = '<label for="' . $k . '_' . $item . '">' . $item . ' </label>';
            switch ($item) {
                case 'taille':
                    $out[] = '<input type="number" ' .
                        'id="' . $k . '_' . $item . '" ' .
                        'name="' . $k . '[' . $item .  ']' . '" ' .
                        'value="' . $value . '" required ' .
                        ($min ? 'min="' . $min . '"': '') .
                        ($max ? 'max="' . $max . '"': '') .
                        ($pas ? 'step="' . $pas . '"': '') .
                        'title="'. ($min ? 'min=' . $min . ' ': '') . ($max ? 'max=' . $max . ' ': '') . ($pas ? 'step=' . $pas . ' ': '') .'"' .
                        '><br>';
                    break;

                case 'type':
                    $out[] = ': ';
                    $out[] = '<span id="' . $k . '_' . $item . '">';
                    foreach (explode('|', $value) as $type) {
                        $out[] = '<input type="checkbox" id="' . $k . '_' . $item . '_' . $type . '" name="' . $k . '[choix][]' . '" value="' . $type . '" ' . (in_array($type, $choix) ? 'checked': '') . '>';
                        $out[] = '<label for="' . $k . '_' . $item . '_' . $type . '">' . $type . ' </label>';
                    }
                    $out[] = '</span>';
                    break;

                case 'comment':
                    $out[] = '<textarea cols="50" readonly disabled required>' . $value . '</textarea><br>';
                    break;

                default:
                    $out[] = '<input type="text" id="' . $k . '_' . $item . '" name="' . $k . '[' . $item .  ']' . '" value="' . $value . '" required><br>';
                    break;
            }

        }

        return $out;
    }

    public function save($filename = null) {
        if (!$filename) $filename = $this->filename;

        //unset($_POST['rq']);
        unset($_POST['senderId']);
        //unset($_POST['envoie']);

        $out = [];
        $error = 0;

        if (!$this->config) $error = 1;
        else {
            $oldConfig = $this->config;
            foreach ($oldConfig as $key => $value) {
                foreach ($value as $k => $v) {
                    if (gettype($v) == 'array') $oldConfig[$key][$k] = [];
                }
            }

            foreach ($this->config = array_replace_recursive($oldConfig, $_POST) as $k => $v) {
                $out[] = '[' . $k . ']';
                foreach ($v as $item => $value) {
                    switch (gettype($value)) {
                        case 'array':
                            foreach ($value as $elem) {
                                $out[] = $item . '[] = "' . $elem . '"';
                            }
                            break;

                        default:
                            $out[] = $item . ' = "' . $value . '"';
                            break;
                    }
                }
                $out[] = "";
            }

            file_put_contents($filename, implode("\n", $out));
        }


        $this->saveError = $error;

        return $this->saveErrorMessage($error);

    }

    public function saveErrorMessage($error) {
        $errorMsg = "";
        switch ($error) {
            case 1:
                $errorMsg = "Vous devez charger la config avant de la sauver !";
                break;
            case 0:
                $errorMsg = "Sauvegarde effectuée";
                break;
        }

        return $errorMsg;
    }

    public function getConfig() {
        if (empty($this->config)){
            return 'Config non chargée';}
        return $this->config;
    }

    function getFilename() {
        return $this->filename;
 }
    function isFileExist(){
        return $this -> fileExist;
    }

    public function getSaveError() {
        return $this->saveError;
    }
}

