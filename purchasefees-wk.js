var PurchaseFees = Calculator.extend({

  ident: 'PurchaseFees',
  variables: [],

  render: function(variables) {

    // Set Class Variables

    this.variables = variables;

    // Setup calculator template


    // Set up 'What do you  want to do'
    what_options = [];
    what_labels = ['Purchase','Remortgage','Sale'];
    for(var i=0; i<what_labels.length; i++) {
      what_options[what_options.length] = { 
          option_id: what_labels[i], 
          option_label: what_labels[i], 
          is_default: false }
    }
    what_options[what_options.length - 1].is_default = 'yes';

    // Set up 'What type of transaction'

    type_options = [];
    type_labels = ['Freehold','Leasehold', 'Shared ownership'];
    for(var i=0; i<type_labels.length; i++) {
      type_options[type_options.length] = { 
        option_id: type_labels[i], 
        option_label: type_labels[i], 
        is_default: false 
      }
    }
    type_options[type_options.length - 1].is_default = 'yes';

    fields = [
      { field_markup: this.renderSelect('do_what', lang.get('calculator_purchasefees_dropdown_what'), what_options) },
      { field_markup: this.renderSelect('transaction_type', lang.get('calculator_purchasefees_dropdown_transaction_type'), type_options) },
       field_markup: this.renderField('transaction_amount', lang.get('calculator_purchasefees_field_transaction_amount'), 'text', 'number', '0') }
    ]

    buttons = [
      { button_markup: this.renderButton('alt', 'view.calculators["' + this.ident + '"].calculate();', lang.get('calculator_button_calculate')) },
      { button_markup: this.renderButton('', 'view.calculators["' + this.ident + '"].clear();', lang.get('calculator_button_clear')) }
    ]

    // Setup results template
     results = [
      { field_markup: this.renderResult(lang.get('calculator_purchasefees_field_purchase_amount'), 'purchase_ammount') },
      { field_markup: this.renderResult(lang.get('calculator_purchasefees_field_fees_payable'), 'fees_payable') }
    ]

    results_buttons = [
      { button_markup: this.renderButton('alt', 'view.calculators["' + this.ident + '"].clear();', lang.get('calculator_button_start_again')) },
      { button_markup: this.renderButton('', 'view.renderContact();', lang.get('calculator_button_contact_us')) }
    ]

    // Parent handles rendering

    this._super(this.ident, fields, buttons, results, results_buttons, false, true);

  },



/**************************************************************************
* NOTE THERE ARE ADDITIONAL CONSTRAINTS ON THE SPREADSHEET UNDER NOTES    *
* ************************************************************************
* Abbreviations:
*     PSC = clients id                                                    *
*     SPN = obtained from spreadsheet notes provided by PSC               *
*                                                                         *
* legal fees calculated by functions, VAT on legal fees SPN               *
***************************************************************************
* REFER-ME comments require clarification from client and  use stupidly   *
* large figures to highlight                                              *
**************************************************************************/

var vat_rate = 0.2;

// stamp duty land tax SPN **
// legal fees calculated by functions SPN ***


  within: function (amount, lower, upper) {
    return amount >= lower && amount <= upper;
  },

  scale_A_fees: function(transaction_amount){
    if (transaction_amount < 100001.0)                return 9999999999.0;                  // REFER-ME refer to PSC
    if (within(transaction_amount,  100001.0,  200000.0)) return  650.0;
    if (within(transaction_amount,  200001.0,  300000.0)) return  750.0;
    if (within(transaction_amount,  300001.0,  400000.0)) return  900.0;
    if (within(transaction_amount,  400001.0,  600000.0)) return 1000.0;
    if (within(transaction_amount,  600001.0,  800000.0)) return 1500.0;
    if (within(transaction_amount,  800001.0, 1000000.0)) return 2500.0;
    if (within(transaction_amount, 1000001.0, 2000000.0)) return 999999999999.9;                // REFER-ME refer to PSC
    if (transaction_amount >= 200001)                 return 999999999999.9;                //REFER-ME refer to PSC
   },

   stamp_duty_scale_B: function(transaction_amount){
    if (transaction_amount < 0)                     return    0;                         //REFER-ME refer to PSC
    if (within(transaction_amount,       0,  125000.0)) return    0;
    if (within(transaction_amount,  125001.0,  250000.0)) return  900.0;
    if (within(transaction_amount,  250001.0,  500000.0)) return 1000.0;
    if (within(transaction_amount,  500001.0, 1000000.0)) return 1500.0;
    if (within(transaction_amount, 1000001.0, 2000000.0)) return 2500.0;
    if (transaction_amount >= 200001.0)               return 999999999999.9;                //REFER-ME refer to PSC
   },

   stamp_duty_scale_C: function(transaction_amount){                                    //REFER-ME refer to PSC
      // total % share < 80%: 0%
      // total % share >= 80%: refer to PSC
   },

   land_registration_scale_D: function(transaction_amount){
    if (transaction_amount < 0)                     return    0;                         //REFER-ME refer to PSC
    if (within(transaction_amount,       0,  125000.0)) return    0;
    if (within(transaction_amount,  125001.0,  250000.0)) return  900.0;
    if (within(transaction_amount,  250001.0,  500000.0)) return 1000.0;
    if (within(transaction_amount,  500001.0, 1000000.0)) return 1500.0;
    if (within(transaction_amount, 1000001.0, 2000000.0)) return 2500.0;
    if (transaction_amount > 200000.0)               return 999999999999.9;                //REFER-ME refer to PSC
   },

   land_registration_scale_E: function(transaction_amount){
    if (transaction_amount < 0)                     return    0;                        //REFER-ME refer to PSC
    if (within(transaction_amount,       0,  100000)) return    0;
    if (within(transaction_amount,  100001.0,  200000)) return  900.0;
    if (within(transaction_amount,  200001.0,  500000)) return 1000.0;
    if (within(transaction_amount,  500001.0, 1000000)) return 1500.0;
    if (transaction_amount > 200000.0)               return 999999999999.0;                //REFER-ME refer to PSC
   },

  legal_fees: function(transaction_type, subtype, transaction_amount, is_same_lender) {
    switch (transaction_type) {
      case "purchase":
        if (subtype == "FH") || (subtype == "LH") fee = scale_A_fees(transaction_amount);
        else fee = 600.0;
        break;
      case "sale":
        if (subtype == "SO") fee = 525.0;
        else fee = scale_A_fees(transaction_amount);
        break;
      case "remortgage":
        fee = 400.0;
        break;
      case "staircasing":
        if (is_same_lender) fee = 350.0;
        else fee = 400.0;
        return fee;
    }
  },

  stamp_duty_land_tax: function(transaction_type, subtype, transaction_amount) {
    switch (transaction_type) {
      case "purchase":
        fee = land_registration_scale_B(transaction_amount);
        break;
      case "sale":
      case "remortgage":
        fee = 0;
        break;
      case "staircasing":
        fee = stamp_duty_scale_C(transaction_amount)
        break;
      return fee;
  },

  land_registration_fee: function(transaction_type, subtype, transaction_amount) {
    switch (transaction_type) {
      case "purchase":
        fee = land_registration_scale_D(transaction_amount);
        break;
      case "sale":
        fee = 0;
        break;
      case "remortgage":
        fee = land_registration_scale_E(transaction_amount);
        break;
      case "staircasing":
        fee = lane_registration_scale_D(transaction_amount);
        break;
    }
  },

  telegraphic_transfer_fee: function(transaction_type, subtype) {
    return = 24.0;
  },

                            //
 //REFER-ME spreadsheet shows blank cells, assumed to be 0
  notice_fees: function(transaction_type, subtype) {
    switch (transaction_type) {
      case "purchase":
        if (subtype == "FH") fee = 0;
        else fee = 150.0;
        break;
      case "sale":
        fee =  0;
        break;
      case "remortgage":
        fee = 150.0;
        break;
      case "staircasing":
        fee = 150.0;
        break;
    }
    return fee;
  },


 //REFER-ME spreadsheet shows blank cells, assumed to be 0
  land_registry_search_fees: function(transaction_type, subtype) {
    if (transaction_type == "sale")
      fee = 0;
    else
      fee = 4.0;
    return fee;
  },


 //REFER-ME spreadsheet shows blank cells, assumed to be 0
  bankruptcy_search_fees: function(transaction_type, subtype, number_of_people) {
    switch (transaction_type) {
      case "sale":
        fee = 0;
        break;
      case "staircasing":
        fee = 2.0 * number_of_people;
        break;
      default:
        fee = 2.0;
        break;
    }
    return fee;
  },

 //REFER-ME spreadsheet shows blank cells, assumed to be 0
  engrossment_fees: function(transaction_type, subtype) {
    switch (transaction_type) {
      case "purchase":
        if (subtype == "SO") || (subtype == "RTB")
          fee = 90.0;
        break;
      case "staircasing":
        fee = 90.0;
        break;
      default:
        fee = 0;
        break
    }
    return fee;
  },


 //REFER-ME spreadsheet shows blank cells, assumed to be 0
  land_registry_office_copy_entries_fees: function(transaction_type, subtype) {
    switch (transaction_type)  {
      case "purchase":
        fee = 0;
        break;
      case "sale":
        if (subtype == "FH")
          fee = 4.0;
        else
          fee = 8.0;
        break;
      case "remortgage":
        fee = 4.0;
        break;
      case "staircasing":
        fee = 4.0;
        break;
    }
    return fee;
  },


 //REFER-ME spreadsheet shows blank cells, assumed to be 0
 land_registry_office_copy_of_lease_fees: function(transaction_type, subtype) {
    switch (transaction_type)  {
      case "purchase":
        if (subtype == "FH")
          fee = 0;
        else
          fee = 24.0;
        break;
      case "sale":
        if (subtype == "SO")
          fee = 0;
        else
          fee = 24.0;
        break;
      case "remortgage":
        fee = 0;
        break;
      case "staircasing":
        fee = 24.0;
        break;
    }
    return fee;
  },


//REFER-ME spreadsheet shows blank cells, assumed to be 0
//NOTE spreadsheet says estimate
management_enquiries_fees: function(transaction_type, subtype) {
     switch (transaction_type)  {
      case "purchase":
        fee = 0;
        break;
      case "sale":
        if (subtype == "SO")
          fee = 250.0;
        else
          fee = 150.0;
        break;
      case "remortgage":
        fee = 150.0;
        break;
      case "staircasing":
        fee = 0;
        break;
    }
    return fee;
},

//REFER-ME spreadsheet shows blank cells, assumed to be 0
search_fees: function(transaction_type, subtype) {
  if (transaction_type == "sale")
    fee = 0;
  else
    fee = 200;
  return fee;
},

/*  valid transaction_types: "purchase", "sale", "remortgage", "staircasing" *
 *  valid subtypes: "FH", "LH", "SO", "RTB"                                  *
 */
calculate_fee: function(transaction_type, subtype, number_of_people, is_same_lender, transaction_amount) {

  //need to calc vat on legal fees, so get a value first
  var lf = legal_fees(transaction_type, subtype);
        legal_fees = legal_fees(transaction_amount) +
        vat_on_legal_fees = (1 + vat_rate) +

  fee = legal_fees +
        vat_on_legal_fees +
        stamp_duty_land_tax(transaction_type, subtype, transaction_amount) +
        land_registration_fee(transaction_type, subtype, transaction_amount) +
        telegraphic_function_key(transaction_type, subtype) +
        notice_fees(transaction_type, subtype) +
        land_registraton_search_fees(transaction_type, subtype) +
        bankruptcy_search_fees(transaction_type, subtype, number_of_people) +
        engrossment_fees(transaction_type, subtype) +
        land_registry_office_copy_entries_fees( transaction_type, subtype) +
        land_registry_copy_of_lease_fees(transaction_type, subtype) +
        management_enquiries_fees(transaction_type, subtype) +
        search_fees(transaction_type, subtype);

    return fee;
},

  calculate: function() {


    if(this.validate()) {
      calculate_fee(transaction, subtype, number_of_people, is_same_lender, transaction_amount);
      this._super();
    }
  },

  validate: function() { return this._super(); },
  clear: function() { this._super(); },
  renderField: function(field_id, field_label, field_type, validation, default_value) { return this._super(field_id, field_label, field_type, validation, default_value); },
  renderSelect: function(field_id, field_label, options) { return this._super(field_id, field_label, options); },
  renderToggle: function(label, toggles) { return this._super(label, toggles); },
  renderDivider: function(content) { return this._super(content); },
  renderButton: function(style, action, label) { return this._super(style, action, label); },
  renderResult: function(label, result_id) { return this._super(label, result_id); },
  prepare: function(calculator_ident, callback) { return this._super(calculator_ident, callback); },
  getVariable: function(variable, default_value) { return this._super(this.ident, variable, default_value); }

});
