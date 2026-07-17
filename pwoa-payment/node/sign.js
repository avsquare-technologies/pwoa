import xrpl from "xrpl";

const chunks = [];

process.stdin.on("data", (chunk) => {
    chunks.push(chunk);
});

process.stdin.on("end", async () => {
    try {
        const input = Buffer.concat(chunks).toString("utf8");

        if (!input.trim()) {
            throw new Error("No transaction input provided");
        }

        let tx;
        try {
            tx = JSON.parse(input);
        } catch (e) {
            throw new Error("Invalid JSON input");
        }

        const seed = process.env.XAHAU_SEED;
        if (!seed) {
            throw new Error("XAHAU_SEED environment variable not set");
        }

        const wallet = xrpl.Wallet.fromSeed(seed);

        const signed = wallet.sign(tx);

        process.stdout.write(
            JSON.stringify({
                tx_blob: signed.tx_blob,
                hash: signed.hash,
            }),
        );

        process.exit(0);
    } catch (err) {
        console.error("Signing Error:", err.message);
        process.exit(1);
    }
});

// import * as xahau from "xahau";

// if (!process.argv[2]) {
//     process.exit(1);
// }

// const input = Buffer.from(process.argv[2], "base64").toString("utf8");

// let tx;
// try {
//     tx = JSON.parse(input);
// } catch {
//     process.exit(1);
// }

// const seed = process.env.XAHAU_SEED;
// if (!seed) {
//     process.exit(1);
// }

// const wallet = xahau.Wallet.fromSeed(seed);
// const signed = wallet.sign(tx);

// process.stdout.write(
//     JSON.stringify({
//         tx_blob: signed.tx_blob,
//         hash: signed.hash
//     })
// );
