function validate_form() {
    // go through each item in val_objs array
    for (item in val_objs)
    {
        // Send the name of this element to the field validation function
        if (!validate_field(item))
        {
            // leave the loop if there has been an error
            // and cancel the submission
            return false
        }
    }

    // Passed all the tests, let the submit proceed
    return true
}

function validate_field(item_in) {
    // get form element
    elem = document.frmAanvraag[item_in];

    // and corresponding Validation object
    curr_obj = val_objs[item_in];

    // set up the RegExp object for the current object
    oRE = new RegExp(curr_obj.regexp, curr_obj.options);

    // and test the value - return true if there is a match
    if (oRE.test(elem.value)) {
        return true;
    } else {
        // test failed: set focus to field in question
        elem.focus();

        // and give corresponding error message to user
        alert(curr_obj.message);

        return false;
    }
}

function ValidObject(regex, msg, opts) // ValidObject
{
    this.regexp = regex;
    this.message = msg;
    this.options = opts;

    return this;
}

function frmAanvraag_init() {
    // alert('nou moe!!');
    // We will create an array of objects with the regular expression,
    // the error message and options.
    // We are using the same name as each field uses in the form to make
    // it easier to access the form values when validating.
    val_objs = new Array; // objects to validate

    var msg;

    regex = "^[A-Z0-9-/\\s]{8,}$";
    msg = "Vul s.v.p. een volledige datum in (dd/mm/jj).";
    val_objs['bezDatum'] = new ValidObject(regex, msg, 'i')

    regex = "^[A-Z0-9-/\\s]{2,}$";
    msg = 'Vul s.v.p. uw naam in.';
    val_objs['bezNaam'] = new ValidObject(regex, msg, 'i');

    regex = "^[0-9-/\\s]{10,}$";
    msg = "Vul s.v.p. uw net- en abonneenummer. Voorbeelden: \r010-1234567 of 0101234567\r06-12345678 of 0612345678";
    val_objs['bezTelefoon'] = new ValidObject(regex, msg, 'i');

    // {2,4} extensie (bijv. .com) tussen 2 en 4 karakters lang
    regex = "^([A\-Z0\-9\\_\\.\\-])+@(([A\-Z0\-9\\-])+?\\.)+([A\-Z0\-9]{2,4}){1}$";
    msg = 'Vul s.v.p. een geldig email adres in.';
    val_objs['bezEmail'] = new ValidObject(regex, msg, 'i');
}
