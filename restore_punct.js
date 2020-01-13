const CDK = "_cdk12332123443234554345665456776567887678998789009890cdk_";
const hyphBrut =
  "ad do=mi=num cum tri=bu=la=rer cla=ma=vi " + CDK + " et in=ten=dit mi=hi";
const textBrut = "Ad Dominum cum tribularer clamavi = et intendit mihi.";

function restorePunct(textBrut, hyphBrut) {
  var orig_splited = textBrut.split(/(=)/g);
  console.log(
    hyphBrut.split(new RegExp("(" + CDK + ")", "g"))
      .map((e, i) => {
        let out = "";
        if (e !== CDK) {
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

restorePunct(hyphBrut, textBrut);