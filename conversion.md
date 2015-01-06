# Easy site utils

## Conversion notes

* rand_pass() - removed dependance on $l['salt'], pass in $salt as 2nd parameter
* salt_string() - removed dependance on $l['salt'], pass in $salt as 2nd parameter
* string_check() - changed behaviour of strip to remove dependance on $l['tags'], pass in string as 2nd parameter
* image_save() - altered use of $l['imagick'], now uses $params[imagick]
* image_valid() - remove use of $l['image-file'], now gets "max upload size"
* date_display(), date_components() - removes use of $l['date-valid-use'], replace with DATE_USA const
* Created output.php and http.php
* Removed get_uri(), get_site(), get_script(), get_root(), get_page(), is_page(), check_input()