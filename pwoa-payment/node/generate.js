import xrpl from "xrpl";

async function generate() {

    const wallet = xrpl.Wallet.generate();

    console.log(
        JSON.stringify({
            address: wallet.address,
            seed: wallet.seed,
            publicKey: wallet.publicKey,
        })
    );
}

generate();
