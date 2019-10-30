
const CDK = "_cdk5555555cdk_";
const BRUT = "ar=bre " + CDK + "=et aut=res";
const ORIG = "Arbre =et Autres...";

var orig_splited = ORIG.split(/(=)/g);

BRUT.split(new RegExp("(" + CDK + ")", "g")).map((e, i) => {
    if (e !== CDK) {
        const lettersH = e.split('');
        const lettersO = orig_splited[i].split('');
        id2 = 0;
        lettersO.map((letter, id) => {
            let letterH = lettersH[id2];
            console.log(letter, id, letterH, id2);

            if (letter.toLowerCase() === letterH) {
                lettersH[id2] = letter;
            } else if (letterH === '=') {
                lettersH[id2] = '-' + letter;
                id2--;
            }

            id2++;
        });
        console.log(lettersH, lettersO);
    }
});

