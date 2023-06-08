# Stripe Identity Verification

Upgrade your Invision Community website with our revolutionary member verification application. Designed to enhance member reputation and instill confidence, this easy-to-use app brings verification to life. Powered by Stripe Identity, it offers a seamless verification process that empowers your members. With a simple verification checkmark displayed next to their names, your community gains a new level of trust and credibility. Take it a step further by configuring Stripe Identity Verification to automatically add verified members to special groups, fostering exclusivity and engagement. Elevate your website's user experience effortlessly with this plug-and-play app that will transform your community interactions.

Read more on Stripe Identity.

Visit Deschutes Design Group LLC for a working example.

## Installation Instructions

1. Begin by downloading the Stripe Identity Verification application from the Invision Community marketplace.
2. Create a Stripe account and log in to your dashboard to access the necessary settings.
3. Sign up for Stripe Identity within your Stripe dashboard.
4. Return to your Invision Community platform and generate an API Rest key that has access to the Stripe Identity Verification webhook endpoint. Make sure to enable Logs for better debugging if needed.
5. Retrieve your API webhook URL. You can find it by navigating to the REST API Reference and locating the Stripe Identity Verification webhook endpoint. The URL will be displayed at the top of the documentation. Remember to append your API Rest key to the end of the URL, like this: https://your-invision-community/api/stripeverification/webhook?key=xxxxx.
6. Go back to your Stripe dashboard and access the Developers area. Add a new webhook and provide your API webhook URL from Invision Community. Make sure to listen only to the "identity.verification_session.verified" event.
7. Once the webhook is created, copy and paste the Signing secret into the Webhook Secret section of the Stripe Identity Verification settings in your Invision Community.
8. Retrieve your Publishable and Secret Key from the Developers API keys section in Stripe. Enter them into the corresponding fields in your Invision Community settings.
9. Save your Invision Community settings to finalize the setup process. You cannot initiate a verification using the checkmark at the top of your Invision Community homepage.