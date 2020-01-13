class ExternalCmds {
  /**
   * @private _xhr
   */

  constructor() {
    this._xhr = new XMLHttpRequest();
  }

  /**
   * @param text:DOMString
   * @param name:String
   * @returns [file:Blob]
   */
  getFileFromString(text) {
    return new File([text], "hyphenation-text", { type: "text/plain" });
  }

  sendHyphenationRequest(text, language) {
    const formPost = new FormData();
    formPost.append("file-to-process", this.getFileFromString(text));
    formPost.append("lang", language);
    this._xhr.open("post", "https://lj4.wilke.xyz/example");
    return new Promise((resolve, reject) => {
      this._xhr.onload = () => {
        resolve(this._xhr.responseText);
      };
      this._xhr.onerror = () => {
        reject(this._xhr);
      };
      this._xhr.send(formPost);
    });
  }
}

class Example {
  /**
   * @private _language;
   * @private _text;
   * @private _ext;
   * @private echapp;
   */

  static get ESCAPE_KEY() {
    return "_cdk12332123443234554345665456776567887678998789009890cdk_";
  }

  constructor(l) {
    this._ext = new ExternalCmds();
    this.language = l;
  }

  set language(l) {
    this._language = l;
  }
  get language() {
    return this._language;
  }

  before(text) {
    this._text = text.replace(/=/g, Example.ESCAPE_KEY);
    text = this._text.split(/(\s)/).map(word => {
      return !/^\s+$/.test(word) && word !== ""
        ? word
            .replace(/([\wáàéèiíìóòúùýæœǽ]+('|’))/gi, "")
            .replace(/([^\wáàéèiíìóòúùýæœǽ]+)/gi, "")
        : "\n";
    });
    return text.join("") + "\n";
  }

  getHyphenation(text) {
    return new Promise((resolve, reject) => {
      this._ext
        .sendHyphenationRequest(this.before(text), this._language)
        .then(text => {
          resolve(this.test(text));
        });
    });
  }

  test(textBrut, hyphBrut) {
    var orig_splited = textBrut.split(/(=)/g);
    console.log(
      hyphBrut
        .split(new RegExp("(" + Example.ESCAPE_KEY + ")", "g"))
        .map((e, i) => {
          let out = "";
          if (e !== Example.ESCAPE_KEY) {
            const lettersH = e.split("");
            const lettersO = orig_splited[i].split("");
            let idH = 0,
              idO = 0;
            for (
              let i = 0;
              i < Math.max(lettersH.length, lettersO.length) + 1;
              i++
            ) {
              let lH = lettersH[idH],
                lO = lettersO[idO];
              if (lO && lO.toLowerCase() === lH) {
                idH++;
                idO++;
                out += lO;
              } else if (lH === "=") {
                idH++;
                out += "-";
              } else if (lO) {
                idO++;
                out += lO;
              }
            }
          } else {
            out += "=";
          }
          return out;
        })
        .join("")
    );
  }

  after(text) {
    console.log(this.test(text, this._text));
    return text
      .split(/\n/)
      .map(word => {
        return word
          .replace(/=/g, "-")
          .replace(new RegExp(Example.ESCAPE_KEY, "g"), "=");
      })
      .join(" ");
  }
}

var events = (function(window, document, $) {
  const LANGUAGE_SELECT = $("#language-select");
  const TEXT = $("#text");
  const RESULT = $("#result");
  const COPY_BUTTON = $("#copy-button");

  const example = new Example(LANGUAGE_SELECT.val());

  let oldContent = "";
  let oldLang = "";

  LANGUAGE_SELECT.change(() => {
    example.language = LANGUAGE_SELECT.val();
    TEXT.keyup();
  });

  TEXT.keyup(() => {
    const content = TEXT.val();
    if (content !== oldContent || LANGUAGE_SELECT.val() !== oldLang) {
      example.getHyphenation(content).then(text => {
        RESULT.val(text);
      });
      oldContent = content;
    }
  });

  COPY_BUTTON.click(() => {
    RESULT.select();
    if (document.execCommand("copy")) {
    } else {
      COPY_BUTTON.text("Erreur");
    }
  });

  return {
    onGoIt: () => {
      TEXT.keyup();
    }
  };
})(window, document, jQuery);
