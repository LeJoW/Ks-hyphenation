class ExternalCmds {
    /**
     * @private _stdout
     */

    constructor() {
        // out = this._stdout; // out is provided by example.js (emscripten)
    }

    sendHyphenationRequest(text, language) {
        const inputLength = text.trim().split("\n").length;
        return new Promise((resolve, reject) => {
            let accumulator = [];
            FS.writeFile("./io-file", text);
            try {
                const exitCode = callMain(["la_VA/hyph_la_VA.dic", "io-file"]);
                out = function (output) {
                    accumulator.push(output);
                    if (accumulator.length === inputLength) {
                        resolve(accumulator.join("\n") + "\n");
                    }
                };
            } catch (e) {
                reject(("Something went wrong with callMain", e));
            }
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
        text = this._text.split(/(\s)/).map((word) => {
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
                .then((text) => {
                    resolve(this.after(text));
                });
        });
    }

    after(hyphBrut) {
        return this._text.length > 0
            ? hyphBrut
                  .replace(/\n/g, " ")
                  .split(new RegExp("(" + Example.ESCAPE_KEY + ")", "g"))
                  .map((e, i) => {
                      let out = "";
                      let outStore = "";
                      if (e !== Example.ESCAPE_KEY) {
                          const lettersH = e.split("");
                          const lettersO = this._text
                              .split(/(=)/g)
                              [i].split("");
                          let idH = 0,
                              idO = 0;
                          for (
                              let i = 0;
                              outStore.length == 0 ||
                              outStore.length < out.length;
                              i++
                          ) {
                              outStore = out;
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
            : "";
    }
}

var events = (function (window, document, $) {
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
            example.getHyphenation(content).then((text) => {
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
        },
    };
})(window, document, jQuery);
