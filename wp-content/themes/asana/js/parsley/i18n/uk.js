//Parsley localization for Ukranian language
//Vitaliy Kiyko
//github.com/mrpsiho

// ParsleyConfig definition if not already set
window.ParsleyConfig = window.ParsleyConfig || {};
window.ParsleyConfig.i18n = window.ParsleyConfig.i18n || {};

// Define then the messages
window.ParsleyConfig.i18n.uk = jQuery.extend(window.ParsleyConfig.i18n.uk || {}, {
  defaultMessage: "Некоректне значення.",
  type: {
    email:        "Вкажіть адресу електронної пошти.",
    url:          "Вкажіть URL адресу.",
    number:       "Вкажіть число.",
    integer:      "Вкажіть ціле число.",
    digits:       "Вкажіть лише цифри.",
    alphanum:     "Вкажіть буквенно-цифрове значення."
  },
  notblank:       "Це поле повинне бути заповнене.",
  required:       "Обов'язкове поле.",
  pattern:        "Це значення некоректне.",
  min:            "Це значення повинне бути не менше %s.",
  max:            "Це значення не повинне бути більше %s.",
  range:          "Це значення повинне бути від %s до %s.",
  minlength:      "Це значення повинне містити не менше %s символів.",
  maxlength:      "Це значення повинне містити не більше %s символів.",
  length:         "Це значення повинне містити від %s до %s символів.",
  mincheck:       "Виберіть не менше %s значень.",
  maxcheck:       "Виберіть не більше %s значень.",
  check:          "Виберіть від %s до %s значень.",
  equalto:        "Це значення повинне співпадати."
});

// If file is loaded after Parsley main file, auto-load locale
if ('undefined' !== typeof window.ParsleyValidator)
  window.ParsleyValidator.addCatalog('uk', window.ParsleyConfig.i18n.uk, true);
