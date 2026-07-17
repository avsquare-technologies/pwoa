# PWOA XRPL EcoSystem
 
PWOA (Pressure Washers of America LLC) is a national pressure washing association that supports contractors, business owners, and industry professionals through memberships, training programs, certifications, events, and community engagement.

To modernize how professional associations operate, PWOA leverages the XRP Ledger (XRPL) to provide verifiable digital memberships, NFT-based event tickets, blockchain-backed certifications, and community reward systems.

The platform utilizes XRPL NFTs to issue secure event tickets and digital membership credentials, while XRPL fungible tokens are used to incentivize participation, training completion, referrals, and community contributions. Members can prove ownership of certifications and memberships through on-chain verification, creating a trusted and transparent ecosystem.

By combining association management with blockchain technology, PWOA demonstrates a practical real-world use case for XRPL beyond payments. The project helps reduce ticket fraud, improve member verification, enhance engagement, and create portable digital identities for industry professionals.

The solution is designed not only for the pressure washing industry but also as a scalable framework that can be adopted by trade associations, professional organizations, educational institutions, and member-driven communities worldwide


# A robust, dual-application microservices ecosystem developed on PHP and Laravel 12.

# PWOA Site (Public Portal):

Stack: Laravel 12, Livewire, TailwindCSS, Vite.
Hosting: Deployed on AWS for high availability.
Database: Uses AWS RDS for secure relational data storage (contractors, users).
Web3 Auth: `xumm-sdk-php` for seamless non-custodial XUMM wallet sign-in.
Role: Public-facing gateway. Handles events, memberships, and securely routes blockchain requests to the payment server.

# Payment XRPL Server (Private Microservice):
Stack: Laravel REST API using `hardcastle/xrpl_php`.
Hosting & Security: Deployed in a private AWS subnet. It is NOT publicly accessible. Only the PWOA public server can communicate with it to trigger operations.
XRPL Engine: Securely handles wallet derivation, XRP payouts, Escrow execution, and custom Token Issuance.
NFT Engine: Manages complex ticket lifecycles (batch minting, decentralized sell offers).