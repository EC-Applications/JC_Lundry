-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 22, 2022 at 06:29 PM
-- Server version: 10.3.35-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
set sql_require_primary_key = off;
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `equinoxmarketing_iselect_v7`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint(20) NOT NULL,
  `current_balance` decimal(24,2) NOT NULL,
  `amount` decimal(24,2) NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `add_ons`
--

CREATE TABLE `add_ons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(24,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `add_ons`
--

INSERT INTO `add_ons` (`id`, `name`, `price`, `created_at`, `updated_at`, `restaurant_id`, `status`) VALUES
(1, 'Fries', '100.00', '2022-05-22 12:42:41', '2022-05-22 12:42:41', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `f_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `f_name`, `l_name`, `phone`, `email`, `image`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`, `zone_id`) VALUES
(1, 'Jhon', 'Doe', '01700000000', 'admin@admin.com', '2021-08-22-61214c4e9e0cb.png', '$2a$12$NQBXTu9.U.jS0McEJPVEZuVcC2eAorHB8vQXCPlbcXuJM/mb9RpfO', 'LM8Ti4yhDa6CjoEVtNVAvtVb366OR4PQT5hsP0uS54RXleKuaqPhZpAqmJlf', '2021-05-08 04:30:27', '2021-08-22 00:56:14', 1, NULL),
(2, 'Monali', 'Khan', '+8801254444444', 'test.employee@gmail.com', '2021-08-22-61228bc40bf5b.png', '$2y$10$.5lp2mMOMOGUJK2aFB6YO.3Gd.eua/KYUDXHQCzWPLHaNVro8rX8S', NULL, '2021-08-22 23:39:16', '2021-08-22 23:39:16', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modules` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `modules`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Master Admin', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_wallets`
--

CREATE TABLE `admin_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `total_commission_earning` decimal(24,2) NOT NULL DEFAULT 0.00,
  `digital_received` decimal(24,2) NOT NULL DEFAULT 0.00,
  `manual_received` decimal(24,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_charge` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Size', '2022-05-22 12:42:21', '2022-05-22 12:42:21');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_settings`
--

CREATE TABLE `business_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_settings`
--

INSERT INTO `business_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'cash_on_delivery', '{\"status\":\"1\"}', '2021-07-01 15:51:17', '2021-07-01 15:51:17'),
(2, 'stripe', '{\"status\":\"1\",\"api_key\":\"sk_test_4eC39HqLyjWDarjtT1zdp7dc\",\"published_key\":\"pk_test_TYooMQauvdEDq54NiTphI7jx\"}', '2021-05-11 03:57:02', '2021-09-08 11:28:06'),
(4, 'mail_config', '{\"name\":\"Jab Chaho\",\"host\":\"mail.experts-global.com\",\"driver\":\"smtp\",\"port\":\"587\",\"username\":\"info@experts-global.com\",\"email_id\":\"info@experts-global.com\",\"encryption\":\"tls\",\"password\":\"K@Yb.3+r1BVs\"}', NULL, '2022-05-22 11:53:18'),
(5, 'fcm_project_id', 'e-food-9e6e3', NULL, NULL),
(6, 'push_notification_key', 'AIzaSyDXk8muN_W2BvcZ45zxeHG1rUAKHeiwnFA', NULL, NULL),
(7, 'order_pending_message', '{\"status\":1,\"message\":\"Your order is successfully placed\"}', NULL, NULL),
(8, 'order_confirmation_msg', '{\"status\":1,\"message\":\"Your order is confirmed\"}', NULL, NULL),
(9, 'order_processing_message', '{\"status\":1,\"message\":\"Your order is started for cooking\"}', NULL, NULL),
(10, 'out_for_delivery_message', '{\"status\":1,\"message\":\"Your food is ready for delivery\"}', NULL, NULL),
(11, 'order_delivered_message', '{\"status\":1,\"message\":\"Your order is delivered\"}', NULL, NULL),
(12, 'delivery_boy_assign_message', '{\"status\":1,\"message\":\"Your order has been assigned to a delivery man\"}', NULL, NULL),
(13, 'delivery_boy_start_message', '{\"status\":1,\"message\":\"Your order is picked up by delivery man\"}', NULL, NULL),
(14, 'delivery_boy_delivered_message', '{\"status\":1,\"message\":\"Order delivered successfully\"}', NULL, NULL),
(15, 'terms_and_conditions', '<p>This is a test Teams &amp; Conditions<br />\r\n<br />\r\nThese terms of use (the &quot;Terms of Use&quot;) govern your use of our website www.evaly.com.bd (the &quot;Website&quot;) and our &quot;StackFood&quot; application for mobile and handheld devices (the &quot;App&quot;). The Website and the App are jointly referred to as the &quot;Platform&quot;. Please read these Terms of Use carefully before you use the services. If you do not agree to these Terms of Use, you may not use the services on the Platform, and we request you to uninstall the App. By installing, downloading and/or even merely using the Platform, you shall be contracting with StackFood and you provide your acceptance to the Terms of Use and other StackFood policies (including but not limited to the Cancellation &amp; Refund Policy, Privacy Policy etc.) as posted on the Platform from time to time, which takes effect on the date on which you download, install or use the Services, and create a legally binding arrangement to abide by the same. The Platforms will be used by (i) natural persons who have reached 18 years of age and (ii) corporate legal entities, e.g companies. Where applicable, these Terms shall be subject to country-specific provisions as set out herein.</p>\r\n\r\n<h3>USE OF PLATFORM AND SERVICES</h3>\r\n\r\n<p>All commercial/contractual terms are offered by and agreed to between Buyers and Merchants alone. The commercial/contractual terms include without limitation to price, taxes, shipping costs, payment methods, payment terms, date, period and mode of delivery, warranties related to products and services and after sales services related to products and services. StackFood does not have any kind of control or does not determine or advise or in any way involve itself in the offering or acceptance of such commercial/contractual terms between the Buyers and Merchants. StackFood may, however, offer support services to Merchants in respect to order fulfilment, payment collection, call centre, and other services, pursuant to independent contracts executed by it with the Merchants. eFood is not responsible for any non-performance or breach of any contract entered into between Buyers and Merchants on the Platform. eFood cannot and does not guarantee that the concerned Buyers and/or Merchants shall perform any transaction concluded on the Platform. eFood is not responsible for unsatisfactory services or non-performance of services or damages or delays as a result of products which are out of stock, unavailable or back ordered.</p>\r\n\r\n<p>StackFood&nbsp;is operating an e-commerce platform and assumes and operates the role of facilitator, and does not at any point of time during any transaction between Buyer and Merchant on the Platform come into or take possession of any of the products or services offered by Merchant. At no time shall StackFood hold any right, title or interest over the products nor shall StackFood have any obligations or liabilities in respect of such contract entered into between Buyer and Merchant. You agree and acknowledge that we shall not be responsible for:</p>\r\n\r\n<ul>\r\n	<li>The goods provided by the shops or restaurants including, but not limited, serving of food orders suiting your requirements and needs;</li>\r\n	<li>The Merchant&quot;s goods not being up to your expectations or leading to any loss, harm or damage to you;</li>\r\n	<li>The availability or unavailability of certain items on the menu;</li>\r\n	<li>The Merchant serving the incorrect orders.</li>\r\n</ul>\r\n\r\n<p>The details of the menu and price list available on the Platform are based on the information provided by the Merchants and we shall not be responsible for any change or cancellation or unavailability. All Menu &amp; Food Images used on our platforms are only representative and shall/might not match with the actual Menu/Food Ordered, StackFood shall not be responsible or Liable for any discrepancies or variations on this aspect.</p>\r\n\r\n<h3>Personal Information that you provide</h3>\r\n\r\n<p>If you want to use our service, you must create an account on our Site. To establish your account, we will ask for personally identifiable information that can be used to contact or identify you, which may include your name, phone number, and e-mail address. We may also collect demographic information about you, such as your zip code, and allow you to submit additional information that will be part of your profile. Other than basic information that we need to establish your account, it will be up to you to decide how much information to share as part of your profile. We encourage you to think carefully about the information that you share and we recommend that you guard your identity and your sensitive information. Of course, you can review and revise your profile at any time.</p>\r\n\r\n<p>You understand that delivery periods quoted to you at the time of confirming the order is an approximate estimate and may vary. We shall not be responsible for any delay in the delivery of your order due to the delay at seller/merchant end for order processing or any other unavoidable circumstances.</p>\r\n\r\n<p>Your order shall be only delivered to the address designated by you at the time of placing the order on the Platform. We reserve the right to cancel the order, in our sole discretion, in the event of any change to the place of delivery and you shall not be entitled to any refund for the same. Delivery in the event of change of the delivery location shall be at our sole discretion and reserve the right to charge with additional delivery fee if required.</p>\r\n\r\n<p>You shall undertake to provide adequate directions, information and authorizations to accept delivery. In the event of any failure to accept delivery, failure to deliver within the estimated time due to your failure to provide appropriate instructions, or authorizations, then such goods shall be deemed to have been delivered to you and all risk and responsibility in relation to such goods shall pass to you and you shall not be entitled to any refund for the same. Our decision in relation to this shall be final and binding. You understand that our liability ends once your order has been delivered to you.</p>\r\n\r\n<p>You might be required to provide your credit or debit card details to the approved payment gateways while making the payment. In this regard, you agree to provide correct and accurate credit/ debit card details to the approved payment gateways for availing the Services. You shall not use the credit/ debit card which is not lawfully owned by you, i.e. in any transaction, you must use your own credit/ debit card. The information provided by you shall not be utilized or shared with any third party unless required in relation to fraud verifications or by law, regulation or court order. You shall be solely responsible for the security and confidentiality of your credit/ debit card details. We expressly disclaim all liabilities that may arise as a consequence of any unauthorized use of your credit/ debit card. You agree that the Services shall be provided by us only during the working hours of the relevant Merchants.</p>\r\n\r\n<h3>ACTIVITIES PROHIBITED ON THE PLATFORM</h3>\r\n\r\n<p>The following is a partial list of the kinds of conduct that are illegal or prohibited on the Websites. StackFood reserves the right to investigate and take appropriate legal action/s against anyone who, in StackFood sole discretion, engages in any of the prohibited activities. Prohibited activities include &mdash; but are not limited to &mdash; the following:</p>\r\n\r\n<ul>\r\n	<li>Using the Websites for any purpose in violation of laws or regulations;</li>\r\n	<li>Posting Content that infringes the intellectual property rights, privacy rights, publicity rights, trade secret rights, or any other rights of any party;</li>\r\n	<li>Posting Content that is unlawful, obscene, defamatory, threatening, harassing, abusive, slanderous, hateful, or embarrassing to any other person or entity as determined by StackFood in its sole discretion or pursuant to local community standards;</li>\r\n	<li>Posting Content that constitutes cyber-bullying, as determined by StackFood in its sole discretion;</li>\r\n	<li>Posting Content that depicts any dangerous, life-threatening, or otherwise risky behavior;</li>\r\n	<li>Posting telephone numbers, street addresses, or last names of any person;</li>\r\n	<li>Posting URLs to external websites or any form of HTML or programming code;</li>\r\n	<li>Posting anything that may be &quot;spam,&quot; as determined by StackFood in its sole discretion;</li>\r\n	<li>Impersonating another person when posting Content;</li>\r\n	<li>Harvesting or otherwise collecting information about others, including email addresses, without their consent;</li>\r\n	<li>Allowing any other person or entity to use your identification for posting or viewing comments;</li>\r\n	<li>Harassing, threatening, stalking, or abusing any person;</li>\r\n	<li>Engaging in any other conduct that restricts or inhibits any other person from using or enjoying the Websites, or which, in the sole discretion of StackFood , exposes eFood or any of its customers, suppliers, or any other parties to any liability or detriment of any type; or</li>\r\n	<li>Encouraging other people to engage in any prohibited activities as described herein.</li>\r\n</ul>\r\n\r\n<p>StackFood&nbsp;reserves the right but is not obligated to do any or all of the following:</p>\r\n\r\n<ul>\r\n	<li>Investigate an allegation that any Content posted on the Websites does not conform to these Terms of Use and determine in its sole discretion to remove or request the removal of the Content;</li>\r\n	<li>Remove Content which is abusive, illegal, or disruptive, or that otherwise fails to conform with these Terms of Use;</li>\r\n	<li>Terminate a user&#39;s access to the Websites upon any breach of these Terms of Use;</li>\r\n	<li>Monitor, edit, or disclose any Content on the Websites; and</li>\r\n	<li>Edit or delete any Content posted on the Websites, regardless of whether such Content violates these standards.</li>\r\n</ul>\r\n\r\n<h3>AMENDMENTS</h3>\r\n\r\n<p>StackFood&nbsp;reserves the right to change or modify these Terms (including our policies which are incorporated into these Terms) at any time by posting changes on the Platform. You are strongly recommended to read these Terms regularly. You will be deemed to have agreed to the amended Terms by your continued use of the Platforms following the date on which the amended Terms are posted.</p>\r\n\r\n<h3>PAYMENT</h3>\r\n\r\n<p>StackFood&nbsp;reserves the right to offer additional payment methods and/or remove existing payment methods at any time in its sole discretion. If you choose to pay using an online payment method, the payment shall be processed by our third party payment service provider(s). With your consent, your credit card / payment information will be stored with our third party payment service provider(s) for future orders. StackFood does not store your credit card or payment information. You must ensure that you have sufficient funds on your credit and debit card to fulfil payment of an Order. Insofar as required, StackFood takes responsibility for payments made on our Platforms including refunds, chargebacks, cancellations and dispute resolution, provided if reasonable and justifiable and in accordance with these Terms.</p>\r\n\r\n<h3>CANCELLATION</h3>\r\n\r\n<p>StackFood&nbsp;can cancel any order anytime due to the foods/products unavailability, out of coverage area and any other unavoidable circumstances.</p>', NULL, '2021-08-22 01:48:01'),
(16, 'business_name', 'Jab Chaho', NULL, NULL),
(17, 'currency', 'PKR', NULL, NULL),
(18, 'logo', '2022-05-22-628a14a6adad5.png', NULL, NULL),
(19, 'phone', '01700000000', NULL, NULL),
(20, 'email_address', 'admin@gmail.com', NULL, NULL),
(21, 'address', 'Karachi, Pakistan', NULL, NULL),
(22, 'footer_text', 'JabChaho @ 2022', NULL, NULL),
(23, 'customer_verification', '0', NULL, NULL),
(24, 'map_api_key', 'AIzaSyDXk8muN_W2BvcZ45zxeHG1rUAKHeiwnFA', NULL, NULL),
(25, 'about_us', '<p>Lorem <strong>ipsum </strong>dolor sit amet, <em><strong>consectetur </strong></em>adipiscing elit. <em>Cras </em>dictum massa et dolor porta, rhoncus faucibus magna elementum. Sed porta mattis mollis. Donec ut est pretium, pretium nibh porttitor, <a href=\"http://google.com\">suscipit </a>metus. Sed viverra felis sed elit vehicula sodales. Nullam ante ante, tristique vel tincidunt ac, egestas eget sem. Sed lorem nunc, pellentesque vel ipsum venenatis, pellentesque interdum orci. Suspendisse mauris dui, accumsan at dapibus sed, volutpat quis erat. Nam fringilla nisl eu nunc lobortis, feugiat posuere libero venenatis. Nunc risus lorem, ornare eget congue in, pretium quis enim. Pellentesque elit elit, pharetra eget nunc at, maximus pellentesque diam.</p>\r\n\r\n<p>Praesent fermentum finibus lacus. Nulla tincidunt lectus sed purus facilisis hendrerit. Maecenas volutpat elementum orci, tincidunt euismod ante facilisis ac. Integer dignissim iaculis varius. Mauris iaculis elit vel posuere pellentesque. Praesent a mi sed neque ullamcorper dignissim sed ut nibh. Sed purus dui, sodales in varius in, accumsan at libero. Vestibulum posuere dui et orci tincidunt, ac consequat felis venenatis.</p>\r\n\r\n<p>Morbi sodales, nisl iaculis fringilla imperdiet, metus tortor semper quam, a fringilla nulla dui nec dolor. Phasellus lacinia aliquam ligula sed porttitor. Cras feugiat eros ut arcu commodo dictum. Integer tincidunt nisl id nisl consequat molestie. Integer elit tortor, ultrices sit amet nunc vitae, feugiat tempus mauris. Morbi volutpat consectetur felis sed porttitor. Praesent in urna erat.</p>\r\n\r\n<p>Aenean mollis luctus dolor, eu interdum velit faucibus eu. Suspendisse vitae efficitur erat. In facilisis nisi id arcu scelerisque bibendum. Nunc a placerat enim. Donec pharetra, velit quis facilisis tempus, lectus est imperdiet nisl, in tempus tortor dolor iaculis dolor. Nunc vitae molestie turpis. Nam vitae lobortis massa. Nam pharetra non felis in porta.</p>\r\n\r\n<p>Vivamus pulvinar diam vel felis dignissim tincidunt. Donec hendrerit non est sed volutpat. In egestas ex tortor, at convallis nunc porttitor at. Fusce sed cursus risus. Nam metus sapien, viverra eget felis id, maximus convallis lacus. Donec nec lacus vitae ex hendrerit ultricies non vel risus. Morbi malesuada ipsum iaculis augue convallis vehicula. Proin eget dolor dignissim, volutpat purus ac, ultricies risus. Pellentesque semper, mauris et pharetra accumsan, ante velit faucibus ex, a mattis metus odio vel ligula. Pellentesque elementum suscipit laoreet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer a turpis sed massa blandit iaculis. Sed aliquet, justo vestibulum euismod rhoncus, nisi dui fringilla sapien, non tempor nunc lectus vitae dolor. Suspendisse potenti.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras dictum massa et dolor porta, rhoncus faucibus magna elementum. Sed porta mattis mollis. Donec ut est pretium, pretium nibh porttitor, suscipit metus. Sed viverra felis sed elit vehicula sodales. Nullam ante ante, tristique vel tincidunt ac, egestas eget sem. Sed lorem nunc, pellentesque vel ipsum venenatis, pellentesque interdum orci. Suspendisse mauris dui, accumsan at dapibus sed, volutpat quis erat. Nam fringilla nisl eu nunc lobortis, feugiat posuere libero venenatis. Nunc risus lorem, ornare eget congue in, pretium quis enim. Pellentesque elit elit, pharetra eget nunc at, maximus pellentesque diam.</p>\r\n\r\n<p>Praesent fermentum finibus lacus. Nulla tincidunt lectus sed purus facilisis hendrerit. Maecenas volutpat elementum orci, tincidunt euismod ante facilisis ac. Integer dignissim iaculis varius. Mauris iaculis elit vel posuere pellentesque. Praesent a mi sed neque ullamcorper dignissim sed ut nibh. Sed purus dui, sodales in varius in, accumsan at libero. Vestibulum posuere dui et orci tincidunt, ac consequat felis venenatis.</p>\r\n\r\n<p>Morbi sodales, nisl iaculis fringilla imperdiet, metus tortor semper quam, a fringilla nulla dui nec dolor. Phasellus lacinia aliquam ligula sed porttitor. Cras feugiat eros ut arcu commodo dictum. Integer tincidunt nisl id nisl consequat molestie. Integer elit tortor, ultrices sit amet nunc vitae, feugiat tempus mauris. Morbi volutpat consectetur felis sed porttitor. Praesent in urna erat.</p>\r\n\r\n<p>Aenean mollis luctus dolor, eu interdum velit faucibus eu. Suspendisse vitae efficitur erat. In facilisis nisi id arcu scelerisque bibendum. Nunc a placerat enim. Donec pharetra, velit quis facilisis tempus, lectus est imperdiet nisl, in tempus tortor dolor iaculis dolor. Nunc vitae molestie turpis. Nam vitae lobortis massa. Nam pharetra non felis in porta.</p>\r\n\r\n<p>Vivamus pulvinar diam vel felis dignissim tincidunt. Donec hendrerit non est sed volutpat. In egestas ex tortor, at convallis nunc porttitor at. Fusce sed cursus risus. Nam metus sapien, viverra eget felis id, maximus convallis lacus. Donec nec lacus vitae ex hendrerit ultricies non vel risus. Morbi malesuada ipsum iaculis augue convallis vehicula. Proin eget dolor dignissim, volutpat purus ac, ultricies risus. Pellentesque semper, mauris et pharetra accumsan, ante velit faucibus ex, a mattis metus odio vel ligula. Pellentesque elementum suscipit laoreet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer a turpis sed massa blandit iaculis. Sed aliquet, justo vestibulum euismod rhoncus, nisi dui fringilla sapien, non tempor nunc lectus vitae dolor. Suspendisse potenti.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras dictum massa et dolor porta, rhoncus faucibus magna elementum. Sed porta mattis mollis. Donec ut est pretium, pretium nibh porttitor, suscipit metus. Sed viverra felis sed elit vehicula sodales. Nullam ante ante, tristique vel tincidunt ac, egestas eget sem. Sed lorem nunc, pellentesque vel ipsum venenatis, pellentesque interdum orci. Suspendisse mauris dui, accumsan at dapibus sed, volutpat quis erat. Nam fringilla nisl eu nunc lobortis, feugiat posuere libero venenatis. Nunc risus lorem, ornare eget congue in, pretium quis enim. Pellentesque elit elit, pharetra eget nunc at, maximus pellentesque diam.</p>\r\n\r\n<p>Praesent fermentum finibus lacus. Nulla tincidunt lectus sed purus facilisis hendrerit. Maecenas volutpat elementum orci, tincidunt euismod ante facilisis ac. Integer dignissim iaculis varius. Mauris iaculis elit vel posuere pellentesque. Praesent a mi sed neque ullamcorper dignissim sed ut nibh. Sed purus dui, sodales in varius in, accumsan at libero. Vestibulum posuere dui et orci tincidunt, ac consequat felis venenatis.</p>\r\n\r\n<p>Morbi sodales, nisl iaculis fringilla imperdiet, metus tortor semper quam, a fringilla nulla dui nec dolor. Phasellus lacinia aliquam ligula sed porttitor. Cras feugiat eros ut arcu commodo dictum. Integer tincidunt nisl id nisl consequat molestie. Integer elit tortor, ultrices sit amet nunc vitae, feugiat tempus mauris. Morbi volutpat consectetur felis sed porttitor. Praesent in urna erat.</p>\r\n\r\n<p>Aenean mollis luctus dolor, eu interdum velit faucibus eu. Suspendisse vitae efficitur erat. In facilisis nisi id arcu scelerisque bibendum. Nunc a placerat enim. Donec pharetra, velit quis facilisis tempus, lectus est imperdiet nisl, in tempus tortor dolor iaculis dolor. Nunc vitae molestie turpis. Nam vitae lobortis massa. Nam pharetra non felis in porta.</p>\r\n\r\n<p>Vivamus pulvinar diam vel felis dignissim tincidunt. Donec hendrerit non est sed volutpat. In egestas ex tortor, at convallis nunc porttitor at. Fusce sed cursus risus. Nam metus sapien, viverra eget felis id, maximus convallis lacus. Donec nec lacus vitae ex hendrerit ultricies non vel risus. Morbi malesuada ipsum iaculis augue convallis vehicula. Proin eget dolor dignissim, volutpat purus ac, ultricies risus. Pellentesque semper, mauris et pharetra accumsan, ante velit faucibus ex, a mattis metus odio vel ligula. Pellentesque elementum suscipit laoreet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer a turpis sed massa blandit iaculis. Sed aliquet, justo vestibulum euismod rhoncus, nisi dui fringilla sapien, non tempor nunc lectus vitae dolor. Suspendisse potenti.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras dictum massa et dolor porta, rhoncus faucibus magna elementum. Sed porta mattis mollis. Donec ut est pretium, pretium nibh porttitor, suscipit metus. Sed viverra felis sed elit vehicula sodales. Nullam ante ante, tristique vel tincidunt ac, egestas eget sem. Sed lorem nunc, pellentesque vel ipsum venenatis, pellentesque interdum orci. Suspendisse mauris dui, accumsan at dapibus sed, volutpat quis erat. Nam fringilla nisl eu nunc lobortis, feugiat posuere libero venenatis. Nunc risus lorem, ornare eget congue in, pretium quis enim. Pellentesque elit elit, pharetra eget nunc at, maximus pellentesque diam.</p>\r\n\r\n<p>Praesent fermentum finibus lacus. Nulla tincidunt lectus sed purus facilisis hendrerit. Maecenas volutpat elementum orci, tincidunt euismod ante facilisis ac. Integer dignissim iaculis varius. Mauris iaculis elit vel posuere pellentesque. Praesent a mi sed neque ullamcorper dignissim sed ut nibh. Sed purus dui, sodales in varius in, accumsan at libero. Vestibulum posuere dui et orci tincidunt, ac consequat felis venenatis.</p>\r\n\r\n<p>Morbi sodales, nisl iaculis fringilla imperdiet, metus tortor semper quam, a fringilla nulla dui nec dolor. Phasellus lacinia aliquam ligula sed porttitor. Cras feugiat eros ut arcu commodo dictum. Integer tincidunt nisl id nisl consequat molestie. Integer elit tortor, ultrices sit amet nunc vitae, feugiat tempus mauris. Morbi volutpat consectetur felis sed porttitor. Praesent in urna erat.</p>\r\n\r\n<p>Aenean mollis luctus dolor, eu interdum velit faucibus eu. Suspendisse vitae efficitur erat. In facilisis nisi id arcu scelerisque bibendum. Nunc a placerat enim. Donec pharetra, velit quis facilisis tempus, lectus est imperdiet nisl, in tempus tortor dolor iaculis dolor. Nunc vitae molestie turpis. Nam vitae lobortis massa. Nam pharetra non felis in porta.</p>\r\n\r\n<p>Vivamus pulvinar diam vel felis dignissim tincidunt. Donec hendrerit non est sed volutpat. In egestas ex tortor, at convallis nunc porttitor at. Fusce sed cursus risus. Nam metus sapien, viverra eget felis id, maximus convallis lacus. Donec nec lacus vitae ex hendrerit ultricies non vel risus. Morbi malesuada ipsum iaculis augue convallis vehicula. Proin eget dolor dignissim, volutpat purus ac, ultricies risus. Pellentesque semper, mauris et pharetra accumsan, ante velit faucibus ex, a mattis metus odio vel ligula. Pellentesque elementum suscipit laoreet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Integer a turpis sed massa blandit iaculis. Sed aliquet, justo vestibulum euismod rhoncus, nisi dui fringilla sapien, non tempor nunc lectus vitae dolor. Suspendisse potenti.</p>', NULL, '2021-07-28 07:09:19'),
(26, 'privacy_policy', '<h2>This is a Demo Privacy Policy</h2>\r\n\r\n<p>This policy explains how StackFood&nbsp;website and related applications (the &ldquo;Site&rdquo;, &ldquo;we&rdquo; or &ldquo;us&rdquo;) collects, uses, shares and protects the personal information that we collect through this site or different channels. StackFood has established the site to link up the users who need foods or grocery items to be shipped or delivered by the riders from the affiliated restaurants or shops to the desired location. This policy also applies to any mobile applications that we develop for use with our services on the Site, and references to this &ldquo;Site&rdquo;, &ldquo;we&rdquo; or &ldquo;us&rdquo; is intended to also include these mobile applications. Please read below to learn more about our information policies. By using this Site, you agree to these policies.</p>\r\n\r\n<h2>How the Information is collected</h2>\r\n\r\n<h3>Information provided by web browser</h3>\r\n\r\n<p>You have to provide us with personal information like your name, contact no, mailing address and email id, our app will also fetch your location information in order to give you the best service. Like many other websites, we may record information that your web browser routinely shares, such as your browser type, browser language, software and hardware attributes, the date and time of your visit, the web page from which you came, your Internet Protocol address and the geographic location associated with that address, the pages on this Site that you visit and the time you spent on those pages. This will generally be anonymous data that we collect on an aggregate basis.</p>\r\n\r\n<h3>Personal Information that you provide</h3>\r\n\r\n<p>If you want to use our service, you must create an account on our Site. To establish your account, we will ask for personally identifiable information that can be used to contact or identify you, which may include your name, phone number, and e-mail address. We may also collect demographic information about you, such as your zip code, and allow you to submit additional information that will be part of your profile. Other than basic information that we need to establish your account, it will be up to you to decide how much information to share as part of your profile. We encourage you to think carefully about the information that you share and we recommend that you guard your identity and your sensitive information. Of course, you can review and revise your profile at any time.</p>\r\n\r\n<h3>Payment Information</h3>\r\n\r\n<p>To make the payment online for availing our services, you have to provide the bank account, mobile financial service (MFS), debit card, credit card information to the StackFood platform.</p>\r\n\r\n<h2>How the Information is collected</h2>\r\n\r\n<h3>Session and Persistent Cookies</h3>\r\n\r\n<p>Cookies are small text files that are placed on your computer by websites that you visit. They are widely used in order to make websites work, or work more efficiently, as well as to provide information to the owners of the site. As is commonly done on websites, we may use cookies and similar technology to keep track of our users and the services they have elected. We use both &ldquo;session&rdquo; and &ldquo;persistent&rdquo; cookies. Session cookies are deleted after you leave our website and when you close your browser. We use data collected with session cookies to enable certain features on our Site, to help us understand how users interact with our Site, and to monitor at an aggregate level Site usage and web traffic routing. We may allow business partners who provide services to our Site to place cookies on your computer that assist us in analyzing usage data. We do not allow these business partners to collect your personal information from our website except as may be necessary for the services that they provide.</p>\r\n\r\n<h3>Web Beacons</h3>\r\n\r\n<p>We may also use web beacons or similar technology to help us track the effectiveness of our communications.</p>\r\n\r\n<h3>Advertising Cookies</h3>\r\n\r\n<p>We may use third parties, such as Google, to serve ads about our website over the internet. These third parties may use cookies to identify ads that may be relevant to your interest (for example, based on your recent visit to our website), to limit the number of times that you see an ad, and to measure the effectiveness of the ads.</p>\r\n\r\n<h3>Google Analytics</h3>\r\n\r\n<p>We may also use Google Analytics or a similar service to gather statistical information about the visitors to this Site and how they use the Site. This, also, is done on an anonymous basis. We will not try to associate anonymous data with your personally identifiable data. If you would like to learn more about Google Analytics, please click here.</p>', NULL, '2021-08-22 01:49:58'),
(27, 'minimum_shipping_charge', '10', NULL, NULL),
(28, 'per_km_shipping_charge', '200', NULL, NULL),
(29, 'digital_payment', '{\"status\":\"1\"}', '2021-07-01 14:27:38', '2021-08-22 00:47:42'),
(30, 'ssl_commerz_payment', '{\"status\":\"1\",\"store_id\":\"custo5cc042f7abf4c\",\"store_password\":\"custo5cc042f7abf4c@ssl\"}', '2021-07-04 15:41:24', '2021-07-04 16:13:54'),
(31, 'razor_pay', '{\"status\":\"1\",\"razor_key\":\"rzp_test_Vio27YvAonerHa\",\"razor_secret\":\"92BIuLdPAkDYx7Bbc9IzqSES\"}', '2021-07-04 15:41:28', '2021-07-04 16:14:37'),
(32, 'paypal', '{\"status\":\"1\",\"paypal_client_id\":\"AabIbRZ97J0GHt0xf_DJj3u1dp6MU9boJGwnRY7OZ6fqBJVsrxd7PaBqqi6OGTYe2e4N4dWkYOkFSNtM\",\"paypal_secret\":\"EIeb5GszxCqanj964p4HYBQ5HMx6TwUGedqY6zdfJqlV-TQMF-cgIP2kZP6d_ZgbS3qjiVJxQH1X6wPt\"}', '2021-07-04 15:41:34', '2021-07-04 16:14:58'),
(33, 'paystack', '{\"status\":\"1\",\"publicKey\":\"sk_test_6eec6c4373b17e031388e60c43153426f15b560e\",\"secretKey\":\"pk_test_9a51c1e76338a611666c2439924b5c21976d7548\",\"paymentUrl\":\"https:\\/\\/api.paystack.co\",\"merchantEmail\":\"showrov2185@gmail.com\"}', '2021-07-04 15:41:42', '2021-07-04 16:16:04'),
(34, 'senang_pay', '{\"status\":\"1\",\"secret_key\":\"3464-669\",\"published_key\":null,\"merchant_id\":\"635161855028588\"}', '2021-07-04 15:41:48', '2021-07-04 16:15:37'),
(35, 'order_handover_message', '{\"status\":1,\"message\":\"Delivery man is on the way\"}', NULL, NULL),
(36, 'order_cancled_message', '{\"status\":1,\"message\":\"Order is canceled by your request\"}', NULL, NULL),
(37, 'timezone', 'Asia/Karachi', NULL, NULL),
(38, 'order_delivery_verification', '0', NULL, NULL),
(39, 'currency_symbol_position', 'left', NULL, NULL),
(40, 'schedule_order', '1', NULL, NULL),
(41, 'app_minimum_version', '0', NULL, NULL),
(42, 'tax', NULL, NULL, NULL),
(43, 'admin_commission', '10', NULL, NULL),
(44, 'country', 'PK', NULL, NULL),
(45, 'app_url', 'https://www.google.com', NULL, NULL),
(46, 'default_location', '{\"lat\":\"23.76469684059319\",\"lng\":\"90.3514959774026\"}', NULL, NULL),
(47, 'twilio_sms', '{\"status\":\"0\",\"sid\":\"ACbf855229b8b2e5d02cad58e116365164\",\"token\":\"46b9779af3aa3e10a3d2fea800cb3b15\",\"from\":\"+12312992176\",\"otp_template\":\"Your otp is #OTP#.\"}', '2021-08-22 01:09:40', '2021-08-22 01:09:40'),
(48, 'nexmo_sms', '{\"status\":0,\"api_key\":\"5923ec09\",\"api_secret\":\"lkryf6xhyBzhftmj\",\"signature_secret\":\"\",\"private_key\":\"\",\"application_id\":\"\",\"from\":\"+8801723019985\",\"otp_template\":\"Your otp is #OTP#.\"}', '2021-08-19 17:00:20', '2021-08-19 17:00:20'),
(49, '2factor_sms', '{\"status\":0,\"api_key\":\"aabf4e9c-f55f-11eb-85d5-0200cd936042\"}', '2021-08-19 17:00:20', '2021-08-19 17:00:20'),
(50, 'msg91_sms', '{\"status\":0,\"template_id\":\"611e267a897b2022f5561052\",\"authkey\":\"365307AW0mawSKCda610b8e5aP1\"}', '2021-08-19 17:00:20', '2021-08-19 17:00:20'),
(51, 'free_delivery_over', '1000', NULL, NULL),
(52, 'maintenance_mode', '0', '2021-09-08 15:44:28', '2022-01-11 13:35:22'),
(53, 'app_minimum_version_ios', NULL, '2021-09-21 22:54:10', '2021-09-21 22:54:10'),
(54, 'app_minimum_version_android', NULL, '2021-09-21 22:54:10', '2021-09-21 22:54:10'),
(55, 'app_url_ios', NULL, '2021-09-21 22:54:10', '2021-09-21 22:54:10'),
(56, 'app_url_android', NULL, '2021-09-21 22:54:10', '2021-09-21 22:54:10'),
(57, 'flutterwave', '{\"status\":1,\"public_key\":\"FLWPUBK_TEST-3f6a0b6c3d621c4ecbb9beeff516c92b-X\",\"secret_key\":\"FLWSECK_TEST-ec27426eb062491500a9eb95723b5436-X\",\"hash\":\"FLWSECK_TEST951e36220f66\"}', '2021-09-21 22:54:10', '2021-09-21 22:54:10'),
(58, 'dm_maximum_orders', '10', '2021-09-24 17:46:13', '2021-09-24 17:46:13'),
(59, 'order_confirmation_model', 'deliveryman', '2021-10-17 00:05:08', '2021-10-17 00:05:08'),
(60, 'popular_food', '1', '2021-10-17 00:05:08', '2021-10-17 00:05:08'),
(61, 'popular_restaurant', '1', '2021-10-17 00:05:08', '2021-10-17 00:05:08'),
(62, 'new_restaurant', '1', '2021-10-17 00:05:08', '2021-10-17 00:05:08'),
(63, 'mercadopago', '{\"status\":1,\"public_key\":\"\",\"access_token\":\"\"}', '2021-10-17 00:05:08', '2021-10-17 00:05:08'),
(64, 'map_api_key_server', 'AIzaSyDXk8muN_W2BvcZ45zxeHG1rUAKHeiwnFA', NULL, NULL),
(66, 'most_reviewed_foods', '1', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(67, 'landing_page_text', '{\"header_title_1\":\"Jab Chaho\",\"header_title_2\":\"Ab karo online order, Jab Chaho Jahan Chaho\",\"header_title_3\":\"Get 10% OFF on your first order\",\"about_title\":\"Jab Chaho is Best Delivery Service Near You\",\"why_choose_us\":\"Why Choose Us?\",\"why_choose_us_title\":\"Lorem ipsum dolor sit amet, consectetur adipiscing elit.\",\"testimonial_title\":\"Trusted by Customer & Restaurant Owner\",\"footer_article\":\"Jab Chaho is Best Delivery Service Near You.\\r\\nGet 10% OFF on your first order\"}', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(68, 'landing_page_links', '{\"app_url_android_status\":null,\"app_url_android\":null,\"app_url_ios_status\":null,\"app_url_ios\":null,\"web_app_url_status\":null,\"web_app_url\":null}', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(69, 'speciality', '[{\"img\":\"clean_&_cheap_icon.png\",\"title\":\"Clean & Cheap Price\"},{\"img\":\"best_dishes_icon.png\",\"title\":\"Best Dishes Near You\"},{\"img\":\"virtual_restaurant_icon.png\",\"title\":\"Your Own Virtual Restaurant\"}]', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(70, 'testimonial', '[{\"img\":\"img-1.png\",\"name\":\"Barry Allen\",\"position\":\"Web Designer\",\"detail\":\"Lorem ipsum dolor sit amet, consectetur adipisicing elit. A\\r\\n                    aliquam amet animi blanditiis consequatur debitis dicta\\r\\n                    distinctio, enim error eum iste libero modi nam natus\\r\\n                    perferendis possimus quasi sint sit tempora voluptatem. Est,\\r\\n                    exercitationem id ipsa ipsum laboriosam perferendis temporibus!\"},{\"img\":\"img-2.png\",\"name\":\"Sophia Martino\",\"position\":\"Web Designer\",\"detail\":\"Lorem ipsum dolor sit amet, consectetur adipisicing elit. A aliquam amet animi blanditiis consequatur debitis dicta distinctio, enim error eum iste libero modi nam natus perferendis possimus quasi sint sit tempora voluptatem. Est, exercitationem id ipsa ipsum laboriosam perferendis temporibus!\"},{\"img\":\"img-3.png\",\"name\":\"Alan Turing\",\"position\":\"Web Designer\",\"detail\":\"Lorem ipsum dolor sit amet, consectetur adipisicing elit. A aliquam amet animi blanditiis consequatur debitis dicta distinctio, enim error eum iste libero modi nam natus perferendis possimus quasi sint sit tempora voluptatem. Est, exercitationem id ipsa ipsum laboriosam perferendis temporibus!\"},{\"img\":\"img-4.png\",\"name\":\"Ann Marie\",\"position\":\"Web Designer\",\"detail\":\"Lorem ipsum dolor sit amet, consectetur adipisicing elit. A aliquam amet animi blanditiis consequatur debitis dicta distinctio, enim error eum iste libero modi nam natus perferendis possimus quasi sint sit tempora voluptatem. Est, exercitationem id ipsa ipsum laboriosam perferendis temporibus!\"}]', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(71, 'landing_page_images', '{\"top_content_image\":\"double_screen_image.png\",\"about_us_image\":\"about_us_image.png\"}', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(72, 'paymob_accept', '{\"status\":\"0\",\"api_key\":null,\"iframe_id\":null,\"integration_id\":null,\"hmac\":null}', '2021-11-15 15:55:37', '2021-11-15 15:55:37'),
(73, 'admin_order_notification', '1', NULL, NULL),
(74, 'show_dm_earning', '1', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(75, 'canceled_by_deliveryman', '0', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(76, 'canceled_by_restaurant', '1', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(77, 'timeformat', '12', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(78, 'toggle_veg_non_veg', '1', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(79, 'toggle_dm_registration', '1', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(80, 'toggle_restaurant_registration', '1', '2022-01-11 13:19:36', '2022-01-11 13:19:36'),
(81, 'recaptcha', '{\"status\":\"0\",\"site_key\":\"6LeIQgYeAAAAAFKEP5fxYiFFtDVO9A5sT-mnmNZc\",\"secret_key\":\"6LeIQgYeAAAAAC8jrBDZvbC4nYFJpVQMuesA0m5t\"}', '2022-01-11 13:33:43', '2022-01-11 13:33:43'),
(82, 'multi_restaurant_order', '1', NULL, NULL),
(83, 'number_of_orders_in_checkout', '100', NULL, NULL),
(84, 'number_of_items_per_order', '100', NULL, NULL),
(85, 'simpaisa', '{\"status\":\"1\",\"merchant_id\":\"2000013\"}', NULL, '2022-05-22 12:30:08');

-- --------------------------------------------------------

--
-- Table structure for table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_restaurant`
--

CREATE TABLE `campaign_restaurant` (
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'def.png',
  `parent_id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `parent_id`, `position`, `status`, `created_at`, `updated_at`, `priority`) VALUES
(1, 'Burger', '2022-05-22-628a2f929ba04.png', 0, 0, 1, '2022-05-22 12:41:54', '2022-05-22 12:41:54', 0),
(2, 'Cheesy', 'def.png', 1, 1, 1, '2022-05-22 12:42:06', '2022-05-22 12:42:06', 0);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reply` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `min_purchase` decimal(24,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `coupon_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `limit` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_uses` bigint(20) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exchange_rate` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `country`, `currency_code`, `currency_symbol`, `exchange_rate`, `created_at`, `updated_at`) VALUES
(1, 'US Dollar', 'USD', '$', '1.00', NULL, NULL),
(2, 'Canadian Dollar', 'CAD', 'CA$', '1.00', NULL, NULL),
(3, 'Euro', 'EUR', '€', '1.00', NULL, NULL),
(4, 'United Arab Emirates Dirham', 'AED', 'د.إ.‏', '1.00', NULL, NULL),
(5, 'Afghan Afghani', 'AFN', '؋', '1.00', NULL, NULL),
(6, 'Albanian Lek', 'ALL', 'L', '1.00', NULL, NULL),
(7, 'Armenian Dram', 'AMD', '֏', '1.00', NULL, NULL),
(8, 'Argentine Peso', 'ARS', '$', '1.00', NULL, NULL),
(9, 'Australian Dollar', 'AUD', '$', '1.00', NULL, NULL),
(10, 'Azerbaijani Manat', 'AZN', '₼', '1.00', NULL, NULL),
(11, 'Bosnia-Herzegovina Convertible Mark', 'BAM', 'KM', '1.00', NULL, NULL),
(12, 'Bangladeshi Taka', 'BDT', '৳', '1.00', NULL, NULL),
(13, 'Bulgarian Lev', 'BGN', 'лв.', '1.00', NULL, NULL),
(14, 'Bahraini Dinar', 'BHD', 'د.ب.‏', '1.00', NULL, NULL),
(15, 'Burundian Franc', 'BIF', 'FBu', '1.00', NULL, NULL),
(16, 'Brunei Dollar', 'BND', 'B$', '1.00', NULL, NULL),
(17, 'Bolivian Boliviano', 'BOB', 'Bs', '1.00', NULL, NULL),
(18, 'Brazilian Real', 'BRL', 'R$', '1.00', NULL, NULL),
(19, 'Botswanan Pula', 'BWP', 'P', '1.00', NULL, NULL),
(20, 'Belarusian Ruble', 'BYN', 'Br', '1.00', NULL, NULL),
(21, 'Belize Dollar', 'BZD', '$', '1.00', NULL, NULL),
(22, 'Congolese Franc', 'CDF', 'FC', '1.00', NULL, NULL),
(23, 'Swiss Franc', 'CHF', 'CHf', '1.00', NULL, NULL),
(24, 'Chilean Peso', 'CLP', '$', '1.00', NULL, NULL),
(25, 'Chinese Yuan', 'CNY', '¥', '1.00', NULL, NULL),
(26, 'Colombian Peso', 'COP', '$', '1.00', NULL, NULL),
(27, 'Costa Rican Colón', 'CRC', '₡', '1.00', NULL, NULL),
(28, 'Cape Verdean Escudo', 'CVE', '$', '1.00', NULL, NULL),
(29, 'Czech Republic Koruna', 'CZK', 'Kč', '1.00', NULL, NULL),
(30, 'Djiboutian Franc', 'DJF', 'Fdj', '1.00', NULL, NULL),
(31, 'Danish Krone', 'DKK', 'Kr.', '1.00', NULL, NULL),
(32, 'Dominican Peso', 'DOP', 'RD$', '1.00', NULL, NULL),
(33, 'Algerian Dinar', 'DZD', 'د.ج.‏', '1.00', NULL, NULL),
(34, 'Estonian Kroon', 'EEK', 'kr', '1.00', NULL, NULL),
(35, 'Egyptian Pound', 'EGP', 'E£‏', '1.00', NULL, NULL),
(36, 'Eritrean Nakfa', 'ERN', 'Nfk', '1.00', NULL, NULL),
(37, 'Ethiopian Birr', 'ETB', 'Br', '1.00', NULL, NULL),
(38, 'British Pound Sterling', 'GBP', '£', '1.00', NULL, NULL),
(39, 'Georgian Lari', 'GEL', 'GEL', '1.00', NULL, NULL),
(40, 'Ghanaian Cedi', 'GHS', 'GH¢', '1.00', NULL, NULL),
(41, 'Guinean Franc', 'GNF', 'FG', '1.00', NULL, NULL),
(42, 'Guatemalan Quetzal', 'GTQ', 'Q', '1.00', NULL, NULL),
(43, 'Hong Kong Dollar', 'HKD', 'HK$', '1.00', NULL, NULL),
(44, 'Honduran Lempira', 'HNL', 'L', '1.00', NULL, NULL),
(45, 'Croatian Kuna', 'HRK', 'kn', '1.00', NULL, NULL),
(46, 'Hungarian Forint', 'HUF', 'Ft', '1.00', NULL, NULL),
(47, 'Indonesian Rupiah', 'IDR', 'Rp', '1.00', NULL, NULL),
(48, 'Israeli New Sheqel', 'ILS', '₪', '1.00', NULL, NULL),
(49, 'Indian Rupee', 'INR', '₹', '1.00', NULL, NULL),
(50, 'Iraqi Dinar', 'IQD', 'ع.د', '1.00', NULL, NULL),
(51, 'Iranian Rial', 'IRR', '﷼', '1.00', NULL, NULL),
(52, 'Icelandic Króna', 'ISK', 'kr', '1.00', NULL, NULL),
(53, 'Jamaican Dollar', 'JMD', '$', '1.00', NULL, NULL),
(54, 'Jordanian Dinar', 'JOD', 'د.ا‏', '1.00', NULL, NULL),
(55, 'Japanese Yen', 'JPY', '¥', '1.00', NULL, NULL),
(56, 'Kenyan Shilling', 'KES', 'Ksh', '1.00', NULL, NULL),
(57, 'Cambodian Riel', 'KHR', '៛', '1.00', NULL, NULL),
(58, 'Comorian Franc', 'KMF', 'FC', '1.00', NULL, NULL),
(59, 'South Korean Won', 'KRW', 'CF', '1.00', NULL, NULL),
(60, 'Kuwaiti Dinar', 'KWD', 'د.ك.‏', '1.00', NULL, NULL),
(61, 'Kazakhstani Tenge', 'KZT', '₸.', '1.00', NULL, NULL),
(62, 'Lebanese Pound', 'LBP', 'ل.ل.‏', '1.00', NULL, NULL),
(63, 'Sri Lankan Rupee', 'LKR', 'Rs', '1.00', NULL, NULL),
(64, 'Lithuanian Litas', 'LTL', 'Lt', '1.00', NULL, NULL),
(65, 'Latvian Lats', 'LVL', 'Ls', '1.00', NULL, NULL),
(66, 'Libyan Dinar', 'LYD', 'د.ل.‏', '1.00', NULL, NULL),
(67, 'Moroccan Dirham', 'MAD', 'د.م.‏', '1.00', NULL, NULL),
(68, 'Moldovan Leu', 'MDL', 'L', '1.00', NULL, NULL),
(69, 'Malagasy Ariary', 'MGA', 'Ar', '1.00', NULL, NULL),
(70, 'Macedonian Denar', 'MKD', 'Ден', '1.00', NULL, NULL),
(71, 'Myanma Kyat', 'MMK', 'K', '1.00', NULL, NULL),
(72, 'Macanese Pataca', 'MOP', 'MOP$', '1.00', NULL, NULL),
(73, 'Mauritian Rupee', 'MUR', 'Rs', '1.00', NULL, NULL),
(74, 'Mexican Peso', 'MXN', '$', '1.00', NULL, NULL),
(75, 'Malaysian Ringgit', 'MYR', 'RM', '1.00', NULL, NULL),
(76, 'Mozambican Metical', 'MZN', 'MT', '1.00', NULL, NULL),
(77, 'Namibian Dollar', 'NAD', 'N$', '1.00', NULL, NULL),
(78, 'Nigerian Naira', 'NGN', '₦', '1.00', NULL, NULL),
(79, 'Nicaraguan Córdoba', 'NIO', 'C$', '1.00', NULL, NULL),
(80, 'Norwegian Krone', 'NOK', 'kr', '1.00', NULL, NULL),
(81, 'Nepalese Rupee', 'NPR', 'Re.', '1.00', NULL, NULL),
(82, 'New Zealand Dollar', 'NZD', '$', '1.00', NULL, NULL),
(83, 'Omani Rial', 'OMR', 'ر.ع.‏', '1.00', NULL, NULL),
(84, 'Panamanian Balboa', 'PAB', 'B/.', '1.00', NULL, NULL),
(85, 'Peruvian Nuevo Sol', 'PEN', 'S/', '1.00', NULL, NULL),
(86, 'Philippine Peso', 'PHP', '₱', '1.00', NULL, NULL),
(87, 'Pakistani Rupee', 'PKR', 'Rs', '1.00', NULL, NULL),
(88, 'Polish Zloty', 'PLN', 'zł', '1.00', NULL, NULL),
(89, 'Paraguayan Guarani', 'PYG', '₲', '1.00', NULL, NULL),
(90, 'Qatari Rial', 'QAR', 'ر.ق.‏', '1.00', NULL, NULL),
(91, 'Romanian Leu', 'RON', 'lei', '1.00', NULL, NULL),
(92, 'Serbian Dinar', 'RSD', 'din.', '1.00', NULL, NULL),
(93, 'Russian Ruble', 'RUB', '₽.', '1.00', NULL, NULL),
(94, 'Rwandan Franc', 'RWF', 'FRw', '1.00', NULL, NULL),
(95, 'Saudi Riyal', 'SAR', 'ر.س.‏', '1.00', NULL, NULL),
(96, 'Sudanese Pound', 'SDG', 'ج.س.', '1.00', NULL, NULL),
(97, 'Swedish Krona', 'SEK', 'kr', '1.00', NULL, NULL),
(98, 'Singapore Dollar', 'SGD', '$', '1.00', NULL, NULL),
(99, 'Somali Shilling', 'SOS', 'Sh.so.', '1.00', NULL, NULL),
(100, 'Syrian Pound', 'SYP', 'LS‏', '1.00', NULL, NULL),
(101, 'Thai Baht', 'THB', '฿', '1.00', NULL, NULL),
(102, 'Tunisian Dinar', 'TND', 'د.ت‏', '1.00', NULL, NULL),
(103, 'Tongan Paʻanga', 'TOP', 'T$', '1.00', NULL, NULL),
(104, 'Turkish Lira', 'TRY', '₺', '1.00', NULL, NULL),
(105, 'Trinidad and Tobago Dollar', 'TTD', '$', '1.00', NULL, NULL),
(106, 'New Taiwan Dollar', 'TWD', 'NT$', '1.00', NULL, NULL),
(107, 'Tanzanian Shilling', 'TZS', 'TSh', '1.00', NULL, NULL),
(108, 'Ukrainian Hryvnia', 'UAH', '₴', '1.00', NULL, NULL),
(109, 'Ugandan Shilling', 'UGX', 'USh', '1.00', NULL, NULL),
(110, 'Uruguayan Peso', 'UYU', '$', '1.00', NULL, NULL),
(111, 'Uzbekistan Som', 'UZS', 'so\'m', '1.00', NULL, NULL),
(112, 'Venezuelan Bolívar', 'VEF', 'Bs.F.', '1.00', NULL, NULL),
(113, 'Vietnamese Dong', 'VND', '₫', '1.00', NULL, NULL),
(114, 'CFA Franc BEAC', 'XAF', 'FCFA', '1.00', NULL, NULL),
(115, 'CFA Franc BCEAO', 'XOF', 'CFA', '1.00', NULL, NULL),
(116, 'Yemeni Rial', 'YER', '﷼‏', '1.00', NULL, NULL),
(117, 'South African Rand', 'ZAR', 'R', '1.00', NULL, NULL),
(118, 'Zambian Kwacha', 'ZMK', 'ZK', '1.00', NULL, NULL),
(119, 'Zimbabwean Dollar', 'ZWL', 'Z$', '1.00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_person_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_histories`
--

CREATE TABLE `delivery_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_man_wallets`
--

CREATE TABLE `delivery_man_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_man_id` bigint(20) UNSIGNED NOT NULL,
  `collected_cash` decimal(24,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_earning` decimal(24,2) NOT NULL DEFAULT 0.00,
  `total_withdrawn` decimal(24,2) NOT NULL DEFAULT 0.00,
  `pending_withdraw` decimal(24,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_men`
--

CREATE TABLE `delivery_men` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `f_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identity_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `earning` tinyint(1) NOT NULL DEFAULT 1,
  `current_orders` int(11) NOT NULL DEFAULT 0,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zone_wise',
  `restaurant_id` bigint(20) DEFAULT NULL,
  `application_status` enum('approved','denied','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `order_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `assigned_order_count` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `min_purchase` decimal(24,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `d_m_reviews`
--

CREATE TABLE `d_m_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_man_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `comment` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_roles`
--

CREATE TABLE `employee_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modules` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food`
--

CREATE TABLE `food` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variations` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_ons` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `choice_options` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(24,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(24,2) NOT NULL DEFAULT 0.00,
  `tax_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `available_time_starts` time DEFAULT NULL,
  `available_time_ends` time DEFAULT NULL,
  `veg` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `avg_rating` double(16,14) NOT NULL DEFAULT 0.00000000000000,
  `rating_count` int(11) NOT NULL DEFAULT 0,
  `rating` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food`
--

INSERT INTO `food` (`id`, `name`, `description`, `image`, `category_id`, `category_ids`, `variations`, `add_ons`, `attributes`, `choice_options`, `price`, `tax`, `tax_type`, `discount`, `discount_type`, `available_time_starts`, `available_time_ends`, `veg`, `status`, `restaurant_id`, `created_at`, `updated_at`, `order_count`, `avg_rating`, `rating_count`, `rating`) VALUES
(1, 'Mighty Zinger', NULL, '2022-05-22-628a2ffce89db.png', 2, '[{\"id\":\"1\",\"position\":1},{\"id\":\"2\",\"position\":2}]', '[{\"type\":\"small\",\"price\":400},{\"type\":\"medium\",\"price\":550}]', '[]', '[\"1\"]', '[{\"name\":\"choice_1\",\"title\":\"Size\",\"options\":[\"small\",\"medium\"]}]', '400.00', '0.00', 'percent', '0.00', 'percent', '00:00:00', '23:59:00', 0, 1, 1, '2022-05-22 12:43:40', '2022-05-22 12:43:40', 0, 0.00000000000000, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_campaigns`
--

CREATE TABLE `item_campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variations` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_ons` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attributes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `choice_options` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(24,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(24,2) NOT NULL DEFAULT 0.00,
  `tax_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `discount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `veg` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mail_configs`
--

CREATE TABLE `mail_configs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `encryption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2021_05_05_081114_create_admins_table', 1),
(5, '2021_05_05_102600_create_attributes_table', 1),
(6, '2021_05_05_102742_create_categories_table', 1),
(7, '2021_05_06_102004_create_vendors_table', 1),
(8, '2021_05_06_153204_create_restaurants_table', 1),
(9, '2021_05_08_113436_create_add_ons_table', 2),
(47, '2021_05_08_123446_create_food_table', 10),
(48, '2021_05_08_141209_create_currencies_table', 10),
(49, '2021_05_09_050232_create_orders_table', 10),
(50, '2021_05_09_051907_create_reviews_table', 10),
(51, '2021_05_09_054237_create_order_details_table', 10),
(52, '2021_05_10_105324_create_mail_configs_table', 10),
(53, '2021_05_10_152713_create_business_settings_table', 10),
(54, '2021_05_11_111722_create_banners_table', 11),
(55, '2021_05_11_134442_create_coupons_table', 11),
(56, '2021_05_12_053344_create_conversations_table', 11),
(57, '2021_05_12_055454_create_delivery_men_table', 12),
(58, '2021_05_12_060138_create_d_m_reviews_table', 12),
(59, '2021_05_12_060527_create_track_deliverymen_table', 12),
(60, '2021_05_12_061345_create_email_verifications_table', 12),
(61, '2021_05_12_061454_create_delivery_histories_table', 12),
(62, '2021_05_12_061718_create_wishlists_table', 12),
(63, '2021_05_12_061924_create_notifications_table', 12),
(64, '2021_05_12_062152_create_customer_addresses_table', 12),
(68, '2021_05_12_062412_create_order_delivery_histories_table', 13),
(69, '2021_05_19_115112_create_zones_table', 13),
(70, '2021_05_19_120612_create_restaurant_zone_table', 13),
(71, '2021_06_07_103835_add_column_to_vendor_table', 14),
(73, '2021_06_07_112335_add_column_to_vendors_table', 15),
(74, '2021_06_08_162354_add_column_to_restaurants_table', 16),
(77, '2021_06_09_115719_add_column_to_add_ons_table', 17),
(78, '2021_06_10_091547_add_column_to_coupons_table', 18),
(79, '2021_06_12_070530_rename_banners_table', 19),
(80, '2021_06_12_091154_add_column_on_campaigns_table', 20),
(87, '2021_06_12_091848_create_item_campaigns_table', 21),
(92, '2021_06_13_155531_create_campaign_restaurant_table', 22),
(93, '2021_06_14_090520_add_item_campaign_id_column_to_reviews_table', 23),
(94, '2021_06_14_091735_add_item_campaign_id_column_to_order_details_table', 24),
(95, '2021_06_14_120713_create_new_banners_table', 25),
(96, '2021_06_15_103656_add_zone_id_column_to_banners_table', 26),
(97, '2021_06_16_110322_create_discounts_table', 27),
(100, '2021_06_17_150243_create_withdraw_requests_table', 28),
(103, '2016_06_01_000001_create_oauth_auth_codes_table', 30),
(104, '2016_06_01_000002_create_oauth_access_tokens_table', 30),
(105, '2016_06_01_000003_create_oauth_refresh_tokens_table', 30),
(106, '2016_06_01_000004_create_oauth_clients_table', 30),
(107, '2016_06_01_000005_create_oauth_personal_access_clients_table', 30),
(108, '2021_06_21_051135_add_delivery_charge_to_orders_table', 31),
(110, '2021_06_23_080009_add_role_id_to_admins_table', 32),
(111, '2021_06_27_140224_add_interest_column_to_users_table', 33),
(113, '2021_06_27_142558_change_column_to_restaurants_table', 34),
(114, '2021_06_27_152139_add_restaurant_id_column_to_wishlists_table', 35),
(115, '2021_06_28_142443_add_restaurant_id_column_to_customer_addresses_table', 36),
(118, '2021_06_29_134119_add_schedule_column_to_orders_table', 37),
(122, '2021_06_30_084854_add_cm_firebase_token_column_to_users_table', 38),
(123, '2021_06_30_133932_add_code_column_to_coupons_table', 39),
(127, '2021_07_01_151103_change_column_food_id_from_admin_wallet_histories_table', 40),
(129, '2021_07_04_142159_add_callback_column_to_orders_table', 41),
(131, '2021_07_05_155506_add_cm_firebase_token_to_vendors_table', 42),
(133, '2021_07_05_162404_add_otp_to_orders_table', 43),
(134, '2021_07_13_133941_create_admin_roles_table', 44),
(135, '2021_07_14_074431_add_status_to_delivery_men_table', 45),
(136, '2021_07_14_104102_create_vendor_employees_table', 46),
(137, '2021_07_14_110011_create_employee_roles_table', 46),
(138, '2021_07_15_124141_add_cover_photo_to_restaurants_table', 47),
(143, '2021_06_17_145949_create_wallets_table', 48),
(144, '2021_06_19_052600_create_admin_wallets_table', 48),
(145, '2021_07_19_103748_create_delivery_man_wallets_table', 48),
(146, '2021_07_19_105442_create_account_transactions_table', 48),
(147, '2021_07_19_110152_create_order_transactions_table', 48),
(148, '2021_07_19_134356_add_column_to_notifications_table', 49),
(149, '2021_07_25_125316_add_available_to_delivery_men_table', 50),
(150, '2021_07_25_154722_add_columns_to_restaurants_table', 51),
(151, '2021_07_29_162336_add_schedule_order_column_to_restaurants_table', 52),
(152, '2021_07_31_140756_create_phone_verifications_table', 53),
(153, '2021_08_01_151717_add_column_to_order_transactions_table', 54),
(154, '2021_08_01_163520_add_column_to_admin_wallets_table', 54),
(155, '2021_08_02_141909_change_time_column_to_restaurants_table', 55),
(157, '2021_08_02_183356_add_tax_column_to_restaurants_table', 56),
(158, '2021_08_04_215618_add_zone_id_column_to_restaurants_table', 57),
(159, '2021_08_06_123001_add_address_column_to_orders_table', 58),
(160, '2021_08_06_123326_add_zone_wise_topic_column_to_zones_table', 58),
(161, '2021_08_08_112127_add_scheduled_column_on_orders_table', 59),
(162, '2021_08_08_203108_add_status_column_to_users_table', 60),
(163, '2021_08_11_193805_add_product_discount_ammount_column_to_orders_table', 61),
(164, '2021_08_12_112511_add_addons_column_to_order_details_table', 62),
(165, '2021_08_12_195346_add_zone_id_to_notifications_table', 63),
(166, '2021_08_14_110131_create_user_notifications_table', 63),
(167, '2021_08_14_175657_add_order_count_column_to_foods_table', 64),
(168, '2021_08_14_180044_add_order_count_column_to_users_table', 64),
(169, '2021_08_19_051055_add_earnign_column_to_deliverymen_table', 65),
(170, '2021_08_19_051721_add_original_delivery_charge_column_to_orders_table', 65),
(171, '2021_08_19_055839_create_provide_d_m_earnings_table', 65),
(172, '2021_08_19_112810_add_original_delivery_charge_column_to_order_transactions_table', 65),
(173, '2021_08_19_114851_add_columns_to_delivery_man_wallets_table', 65),
(174, '2021_08_21_162616_change_comission_column_to_restaurants_table', 66),
(175, '2021_06_17_054551_create_soft_credentials_table', 67),
(176, '2021_08_22_232507_add_failed_column_to_orders_table', 67),
(177, '2021_09_04_172723_add_column_reviews_section_to_restaurants_table', 68),
(178, '2021_09_11_164936_change_delivery_address_column_to_orders_table', 68),
(179, '2021_09_11_165426_change_address_column_to_customer_addresses_table', 68),
(180, '2021_09_23_120332_change_available_column_to_delivery_men_table', 69),
(181, '2021_10_03_214536_add_active_column_to_restaurants_table', 70),
(182, '2021_10_04_123739_add_priority_column_to_categories_table', 70),
(183, '2021_10_06_200730_add_restaurant_id_column_to_demiverymen_table', 70),
(184, '2021_10_07_223332_add_self_delivery_column_to_restaurants_table', 70),
(185, '2021_10_11_214123_change_add_ons_column_to_order_details_table', 70),
(186, '2021_10_11_215352_add_adjustment_column_to_orders_table', 70),
(187, '2021_10_24_184553_change_column_to_account_transactions_table', 71),
(188, '2021_10_24_185143_change_column_to_add_ons_table', 71),
(189, '2021_10_24_185525_change_column_to_admin_roles_table', 71),
(190, '2021_10_24_185831_change_column_to_admin_wallets_table', 71),
(191, '2021_10_24_190550_change_column_to_coupons_table', 71),
(192, '2021_10_24_190826_change_column_to_delivery_man_wallets_table', 71),
(193, '2021_10_24_191018_change_column_to_discounts_table', 71),
(194, '2021_10_24_191209_change_column_to_employee_roles_table', 71),
(195, '2021_10_24_191343_change_column_to_food_table', 71),
(196, '2021_10_24_191514_change_column_to_item_campaigns_table', 71),
(197, '2021_10_24_191626_change_column_to_orders_table', 71),
(198, '2021_10_24_191938_change_column_to_order_details_table', 71),
(199, '2021_10_24_192341_change_column_to_order_transactions_table', 71),
(200, '2021_10_24_192621_change_column_to_provide_d_m_earnings_table', 71),
(201, '2021_10_24_192820_change_column_type_to_restaurants_table', 71),
(202, '2021_10_24_192959_change_column_type_to_restaurant_wallets_table', 71),
(203, '2021_11_02_123115_add_delivery_time_column_to_restaurants_table', 71),
(204, '2021_11_02_170217_add_total_uses_column_to_coupons_table', 71),
(205, '2021_12_01_131746_add_status_column_to_reviews_table', 72),
(206, '2021_12_01_135115_add_status_column_to_d_m_reviews_table', 72),
(207, '2021_12_13_125126_rename_comlumn_set_menu_to_food_table', 73),
(208, '2021_12_13_132438_add_zone_id_column_to_admins_table', 73),
(209, '2021_12_18_174714_add_application_status_column_to_delivery_men_table', 73),
(210, '2021_12_20_185736_change_status_column_to_vendors_table', 73),
(211, '2021_12_22_114414_create_translations_table', 73),
(212, '2022_01_05_133920_add_sosial_data_column_to_users_table', 73),
(213, '2022_01_05_172441_add_veg_non_veg_column_to_restaurants_table', 73),
(214, '2022_01_20_151449_add_restaurant_id_column_on_employee_roles_table', 74),
(215, '2022_01_31_124517_add_veg_column_to_item_campaigns_table', 75),
(216, '2022_02_01_101248_change_coupon_code_column_length_to_coupons_table', 75),
(217, '2022_02_01_104336_change_column_length_to_notifications_table', 75),
(218, '2022_02_06_160701_change_column_length_to_item_campaigns_table', 75),
(219, '2022_02_06_161110_change_column_length_to_campaigns_table', 75),
(220, '2022_02_07_091359_add_zone_id_column_on_orders_table', 75),
(221, '2022_02_07_101547_change_name_column_to_categories_table', 75),
(222, '2022_02_07_152844_add_zone_id_column_to_order_transactions_table', 75),
(223, '2022_02_07_162330_add_zone_id_column_to_users_table', 75),
(224, '2022_02_07_173644_add_column_to_food_table', 75),
(225, '2022_02_07_181314_add_column_to_delivery_men_table', 75),
(226, '2022_02_07_183001_add_total_order_count_column_to_restaurants_table', 75),
(227, '2022_05_16_130158_add_group_id_column_to_orders_table', 75);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tergat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('023ca3c59b072fb76e068d34a0c33604fa63b6a69d2cdb5debc5ea27e88419e33140946d08a5bcce', 16, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:08:36', '2021-10-17 00:08:36', '2022-10-17 00:08:36'),
('044b60a6490d04cea5c65dfde8952faeeb3d05ca2e45ba1e5418170cbcc166c9f22deddb2a7b4078', 32, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:01:48', '2022-01-11 13:01:48', '2023-01-11 13:01:48'),
('0b8d8d02d3dfdf7747a2b9ec0b9b3d91fb2748bfe1940e3c3215c00420a2db36e18a1516d193cbbf', 6, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:43:52', '2021-08-21 21:43:52', '2022-08-21 21:43:52'),
('0b93decdc869ccc0af71533b3e495c79e3f12f8e062c9b4ba5b4dd42fa634d085209c4d52fcd04c3', 22, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 15:55:11', '2021-10-17 15:55:11', '2022-10-17 15:55:11'),
('0d13a21cd76d70d54f3683aa0de8ef2d3f0dca1419e9eedfecc8853f3d09c27e61528023f4cdf466', 16, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:39:40', '2021-10-17 00:39:40', '2022-10-17 00:39:40'),
('110f9469b3815d5344cd1d460347e17d9ccb40e730c738a1f94abac827028c7d1d8aee6a61e47bd8', 27, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:17:18', '2022-01-11 14:17:18', '2023-01-11 14:17:18'),
('15a4c933d9f0645b6a4ee5739fe961bf8bdc821558a25e93cfd13fa9f9291f79188414b5a3e44f5e', 20, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:39:46', '2021-10-17 00:39:46', '2022-10-17 00:39:46'),
('1aada5437ea14af05a155250cd1abaabee40bcb9f4d35edf6d8ef96bdfb04ed6f62a8e15c7594d8d', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:29:31', '2022-01-11 12:29:31', '2023-01-11 12:29:31'),
('1b905c9cf244f4ed3d58b4eba6ae6736881d8175abc6ba60044cd28cbf27cf11a1a84e059c971047', 15, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 08:52:21', '2021-08-22 08:52:21', '2022-08-22 08:52:21'),
('1c2410e6554e79dcc983e0403331d813e919bc03328781ecd61ed477648e145d54431fa28320df5e', 32, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:18:08', '2022-01-11 14:18:08', '2023-01-11 14:18:08'),
('1f856facc3066a69bea79422da0e9aeb984e8b72ed8b9c7ce9e69254e312963e75987cc537ed2cbf', 41, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:16:27', '2022-01-20 18:16:27', '2023-01-20 18:16:27'),
('21e840ba1317a972881214cbf6042405680658980149187fce65f473f9f32d9bc6de9949cba54776', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:52:14', '2021-08-21 21:52:14', '2022-08-21 21:52:14'),
('229640324d31c9630dc2d8ad34e74c8c7e9e7799d89f529940bb67c0dc90b4241ca02b6259e02299', 30, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:33:16', '2022-01-11 12:33:16', '2023-01-11 12:33:16'),
('23e6f33b9e7b7186de91463898b3ddac698f8cd018f2a731de3ff99a1cc9f1f357eabd6128fa1c2b', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:30:03', '2022-01-11 12:30:03', '2023-01-11 12:30:03'),
('2a6a1d09e39ec55ae28e24d2b6a1be172ecee6bc75a3b93bb223bfaac8b1532e7ea52b272f8edf4e', 27, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:10:21', '2022-01-11 13:10:21', '2023-01-11 13:10:21'),
('2aae4d5f2c66df56edb17a80046aaaf1a1b4fd2afe8d5c2618900a24627149d106e6ab3f7e13b309', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:51:29', '2022-01-11 12:51:29', '2023-01-11 12:51:29'),
('2c2fbed1300b990730e1ebe269d0f8389d6475a9dfa5154136a2c399924fa5ad2e7bf18f184e86e4', 36, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:02:13', '2022-01-11 14:02:13', '2023-01-11 14:02:13'),
('2cd7d29816f1a25c1d51907cff12efc4b822a5010d491cd33637370e014d635c0d80056727138d78', 54, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:27:56', '2022-05-22 12:27:56', '2023-05-22 17:27:56'),
('2d5bc506930432d51c88faa09cdf02dab3b2cc542017ba0c5c54b6c22242995531eb8e16d4bdd0b2', 31, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:34:36', '2022-01-11 12:34:36', '2023-01-11 12:34:36'),
('2dbf5364f97089c8286d83975841ad22e09e4b16cba4b93da015961797402d0496ea5574a7f673e3', 10, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:27:21', '2021-08-21 22:27:21', '2022-08-21 22:27:21'),
('2de3b13e6c9b55189fd60d4b8904a873cd8bd81818829923dba943ef2aa67a45022f4d90f57d3694', 4, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 13:38:51', '2021-08-21 13:38:51', '2022-08-21 13:38:51'),
('2f52931e650c39ea22fa7f9d017db8c17e02c8c7d4f4ad9f7cadac77dc87dc36f82795c6c1c8e046', 3, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:52:38', '2022-05-22 12:52:38', '2023-05-22 17:52:38'),
('3ed43541ac9335c218dba0365d86edfae056c3d6be6088f110b541ac0d52d2a73f12583e4d87b4d6', 27, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:08:09', '2022-01-11 13:08:09', '2023-01-11 13:08:09'),
('40f5a1845dac4c10ed6f42f65e2472daf3df8f0b2a618bc4dea6294a65199119249dbb04978e5f7f', 33, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:21:50', '2022-01-11 14:21:50', '2023-01-11 14:21:50'),
('41447a97a7013cad00175f13b4912db77f5f24cdc0438579d6e58020552d589cd8838d09441759e0', 28, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:33:57', '2022-01-11 13:33:57', '2023-01-11 13:33:57'),
('41714c1275261f3d051bdbf56c754735a860586e341f912d781ec9b654c37d4d2ccc0a22f6db2056', 28, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:29:42', '2022-01-11 13:29:42', '2023-01-11 13:29:42'),
('423df50d71b464b0cd68b2eadf67be4036035c8d426e0d0faebca7c58baffcb7596f8c2c865b301e', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:24:19', '2022-01-11 12:24:19', '2023-01-11 12:24:19'),
('45d14cf392094872b4840ac1ef60a951b1ac3cf21afa40cfb4d8b4b82585afd5c60969532e75930b', 28, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:24:47', '2022-01-20 18:24:47', '2023-01-20 18:24:47'),
('46a9ef40bead8ffd39a591b908a7380160b5ee5b50bdd4741393378049bbfa09c4dda7de0a0b78c1', 3, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 14:07:38', '2021-08-21 14:07:38', '2022-08-21 14:07:38'),
('47f4b857f2d2ea93f10256116feff7d544304b4b5efd109767d222eb8c8bdd1e56d9174dd20af8c5', 41, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:16:07', '2022-01-20 18:16:07', '2023-01-20 18:16:07'),
('4c88629a6afc254a25c713ecb00703e8313da8ad0fb01cb5618389c4e82f2f1058855b8ccebf21a8', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:24:58', '2022-01-11 12:24:58', '2023-01-11 12:24:58'),
('4f9bf0e57db9fc243ddc96b8b8bbd342154dfed7ba620123b5b4b47a578a147d6b51a4152a752c52', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:53:00', '2021-08-21 22:53:00', '2022-08-21 22:53:00'),
('56024c1c6d053ceb7cc88145373f149c0a369f2af024671d2a1b5ef01d4480d6c6bffd5d1e5a18c4', 19, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:38:08', '2021-10-17 00:38:08', '2022-10-17 00:38:08'),
('56de13654e840636117eb81d523564ec4f5bdb42548fa844d8ae5ee1eeaae19b520f08006340d8f0', 28, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:13:48', '2022-01-20 18:13:48', '2023-01-20 18:13:48'),
('5a2ed697331792c74eb1f298981dc2627ce4f064ed4181c53f875964015f5fe4ac1ae88edb6a05f9', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 20:54:06', '2021-08-21 20:54:06', '2022-08-21 20:54:06'),
('5aed20e6b70d9293ce3e601f6f466c0c1666ad1503770a3cc1ad00c9b4520286c0f99942f36d5020', 16, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-12-05 18:50:23', '2021-12-05 18:50:23', '2022-12-05 18:50:23'),
('5b46adddb3d72697bf906d02b0c79412ff3fa5b345a2a06fa7bf004b4fa54cbac1c45f215e26d6a4', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:15:38', '2021-08-21 22:15:38', '2022-08-21 22:15:38'),
('5b798f97bedd1de8fe8b46f4ae7be275dbb233c3f8a4edb3a568a63dbfded63bfd3ff1990251b775', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:37:16', '2021-08-21 22:37:16', '2022-08-21 22:37:16'),
('5c2a597e90b0854c163706fc0fc1590b3186fa81fa2f3106583c83083d69dde30f3432166b942f66', 12, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 00:57:42', '2021-08-22 00:57:42', '2022-08-22 00:57:42'),
('60bfd2404aca1b77f3ab498046056fdd2b22a705b26982fc30dd9b62ff556cc4d50d9dbbf8d87bf3', 1, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:54:33', '2022-05-22 12:54:33', '2023-05-22 17:54:33'),
('6255bbdb522f05b6f1c4ce3ebf0c01b92a6d2e8e16f3bc26441329223d55bda8e22a7a23bfee7247', 8, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:49:59', '2021-08-21 21:49:59', '2022-08-21 21:49:59'),
('63173bea42d423f9902b4096549a629c856840c559703c6a2cc4b22545b1999ceb63dc21e1ce9aff', 32, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:17:55', '2022-01-11 14:17:55', '2023-01-11 14:17:55'),
('647e139eac5c84543a1b097d200dc2a8f0dec43063aa85f7aa38d09d2489bb44a2d6a2eae5e3d627', 41, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:13:53', '2022-01-20 18:13:53', '2023-01-20 18:13:53'),
('655bf32d53dc8ab641d960b5128abf77c7ae2ba8733b93ec4e6a1ffc9876cf4435358e02a6d7d590', 7, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:09:00', '2021-10-17 00:09:00', '2022-10-17 00:09:00'),
('659af847907494d1445ba9581d0b151dcbde857aafd477b96cbf34414ab28d8196b38a25b48dac64', 24, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-11-15 16:14:40', '2021-11-15 16:14:40', '2022-11-15 16:14:40'),
('681a28a9c3803230235584da221a2b795a3061d3ec47e63d174e925f5d9bff664096cddcc47d29b8', 7, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:38:13', '2021-10-17 00:38:13', '2022-10-17 00:38:13'),
('6843f740fce8de5299db8ac8a78aa24a7613455d468fc79efd56350af49b1cd41aa50bb2e31319b3', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:30:27', '2022-01-11 12:30:27', '2023-01-11 12:30:27'),
('693b85dcb116b879aa2134abfa2e10a60e9c19b42ab4683a08ed60106172383bfbc9a3cc9ec7be78', 32, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:59:03', '2022-01-11 12:59:03', '2023-01-11 12:59:03'),
('6b41934bdd28a6efb0679b78c95c042e1f33e8ba1759e37fb47ab6a97440c95a12eaf4997f16ed62', 35, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:55:33', '2022-01-11 13:55:33', '2023-01-11 13:55:33'),
('6b5ecf3cb209d2ee5f8042cba05d34d0ce9d7fde91f440c7ebeeb619c34d458868c3f739ccd7538a', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:33:27', '2021-08-21 22:33:27', '2022-08-21 22:33:27'),
('6b94a860b4291446dee68e61be393c4e6287d0925e42ba5839ef42f6544f10b0c693fb46ab80b687', 11, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:50:06', '2021-08-21 22:50:06', '2022-08-21 22:50:06'),
('6c43bb2a75783731f4bcca78543d5f99043797b01d1e992bb31949eb14aa6a3521fcddea4f2a04c6', 27, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:08:39', '2022-01-11 12:08:39', '2023-01-11 12:08:39'),
('6e6d75c437c140aebefcee6cbfb6b965163fcaee59201daddfb77acb6f68b46f81e7a29b2fc5feaa', 20, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 15:37:57', '2021-10-17 15:37:57', '2022-10-17 15:37:57'),
('6f0f7cb4ee8382210436ba075581c620f4ccc1e42a7dbf39642f7d64978ef4efddb8bd13069cdde4', 40, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:04:01', '2022-01-20 18:04:01', '2023-01-20 18:04:01'),
('70540ab3c72beda25f1f62fd14d8f6e644becb0df1ea7f55bcdc54981a20c0e9cf4bd2fcd223caca', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:05:42', '2022-01-11 13:05:42', '2023-01-11 13:05:42'),
('74019d5668e7f182acbf65dcdfa21751463fb433a4830b7c972b8d4535ceed9327395b0fec0cbe8a', 16, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-11-15 16:10:33', '2021-11-15 16:10:33', '2022-11-15 16:10:33'),
('7b5168baec4eb43a23f7583dd3e6648032fb6d33b9afda6dd490bd6acee813e6eb4f41c4fab88352', 3, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 14:04:24', '2021-08-21 14:04:24', '2022-08-21 14:04:24'),
('7c3889068a359f7dc093256d6bf4723cbd8846d986abeb056a5afe6200cb2afe6a6036e7d789f284', 19, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:42:13', '2021-10-17 00:42:13', '2022-10-17 00:42:13'),
('80a11e77bac86e65a5c3ea31281e73d80c70056e1dc9f11a9dfb25c71b044e9a1dfab452618e126f', 39, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:23:02', '2022-01-11 14:23:02', '2023-01-11 14:23:02'),
('8b8b0f39e40c9ce998d9ba95c9c21be929398ee547747ded5f37b9b50af194662049402168d460ac', 1, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-19 22:32:01', '2021-08-19 22:32:01', '2022-08-19 22:32:01'),
('8ebc9c57d01dc55c5e32ce7c6cce0d2e323919c33c0d8516d261e51495501182eb9e20201deb019d', 15, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-23 00:04:13', '2021-08-23 00:04:13', '2022-08-23 00:04:13'),
('8f0e0584255fdabeb37e73cf5163e00d2b7544242be96a548e9575eb789c1d81e8c1f9130037e8d6', 10, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:25:59', '2021-08-21 22:25:59', '2022-08-21 22:25:59'),
('923f25f49983609ffa24f70918c65bc02fb3fa8c7cf4d4d0d665a606795db702eac38fc1fd71cb88', 1, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:42:59', '2022-05-22 12:42:59', '2023-05-22 17:42:59'),
('929d6d154d90c8975ca5b8d4536b78bad421cb4e4626e0f8d1d91c1193c92c93f8dcadcc8a7a1caf', 4, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 02:46:03', '2021-08-22 02:46:03', '2022-08-22 02:46:03'),
('954fb6380a9e0a8822fa99c1325dd1ecebbc71d1012c34e3a789dc74e9c6556e0de19ecc1a4f8d73', 7, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:40:29', '2021-08-21 21:40:29', '2022-08-21 21:40:29'),
('9587e99311c72535f9d92db4771a49d2e834b8a04a72c64b709a0358053430765fe1895e483d8446', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:50:30', '2022-01-11 12:50:30', '2023-01-11 12:50:30'),
('972653d1a429105338fba0de78571376b9b0b6b6f7b669f830f8a7fe86640dab54b02b708aa2cbcc', 43, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:18:18', '2022-01-20 18:18:18', '2023-01-20 18:18:18'),
('9a0916e0482aed0e62b54f56ddb212c2813cbc273e40ee153b0191139fd6dd30e87e0cd1dbb7599c', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:46:04', '2022-01-11 12:46:04', '2023-01-11 12:46:04'),
('9c6364e24d6961277e99eea32f4625a8afdb68c375ac0667fa1ce525bb8073e6cdca29a46cc14b08', 44, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:40:03', '2022-01-20 18:40:03', '2023-01-20 18:40:03'),
('9d906cc2e7d4d9827eaf29e4da6943b0bad04633cb346c7b9e87e46df59ed5ea6bb80df4617cfc27', 4, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 14:02:47', '2021-08-21 14:02:47', '2022-08-21 14:02:47'),
('9db4434fa79442639fcd6507aa6c36ec7160e6329555c4f1e68f506519df9d91821e569489f74430', 34, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:17:20', '2022-01-11 13:17:20', '2023-01-11 13:17:20'),
('9ebd4aea7656ede8d7a907fc13497955b765e13fa873cb19077b4e913f3b95dfc80f2f8d05d6d79e', 4, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 23:58:14', '2021-08-22 23:58:14', '2022-08-22 23:58:14'),
('a2ae8b6df1c6d60eea28d63046fda8bb66cda58a693cb1fbcf9ed995e008f6554df983d12938f90a', 4, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:39:03', '2021-08-21 22:39:03', '2022-08-21 22:39:03'),
('a43292c97883f84b2bbf5edba4e23ac78a9b70a8eddaac1feba09f3012d5e3112dda0f36fcca8db4', 38, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:22:31', '2022-01-11 14:22:31', '2023-01-11 14:22:31'),
('a7a9bc0c83e07d54cf8a9f7258ed7d0df2246104c39c0054d43f66a852f480d40c1777e13d283bd4', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:02:22', '2022-01-11 13:02:22', '2023-01-11 13:02:22'),
('a9bdfc3d8e8b295d3dcbbb67429c97e0bd776a4e27720cf89b5d325995b634ab2b9891800e29a713', 28, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:12:24', '2022-01-11 12:12:24', '2023-01-11 12:12:24'),
('ad16d295bc7c802c83a8e8264fa96563cf7d4393b475ceb3175ef8844998ab375c560242dfa09176', 24, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-11-15 15:37:35', '2021-11-15 15:37:35', '2022-11-15 15:37:35'),
('ad4a07ff77a86a13aca8ebad5b2d7776178b8f0535f4ec442d6b8ceeade9a21ccea098b71ba2132a', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 01:52:22', '2021-08-22 01:52:22', '2022-08-22 01:52:22'),
('ae72ea210d5a93d6679857887b050d054991b544d451645ed1ab17d839907886d78ded62711941fa', 2, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-20 14:18:53', '2021-08-20 14:18:53', '2022-08-20 14:18:53'),
('aee72ff77720f9469631f2942261719795373b9eb294f5fea5912d99e33bc00cfc802fe53f3f681b', 32, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:49:55', '2022-01-11 12:49:55', '2023-01-11 12:49:55'),
('b3dbd8eaecf617089ed5d69696171e797ff51954267626522cd8bca5bc00067960a29767ea761ac6', 10, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:27:46', '2021-08-21 22:27:46', '2022-08-21 22:27:46'),
('b484ee8f9a6f4741f53051b93a48b2c8a0d959df960ecaed8acb71f400190d112abc0eba0377ab28', 21, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 15:10:11', '2021-10-17 15:10:11', '2022-10-17 15:10:11'),
('b79a4f7d9dd66b4bb08f2db07975bd1b5a2f5c650e71051949f8eee54dfb1193219e45af7823fd84', 6, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:38:37', '2021-08-21 21:38:37', '2022-08-21 21:38:37'),
('bb2cd3354f11d41e5b8791cbc5c1bd21e437fdd27702eb349b3c19d6e330fb5997ade03c5c88c8ec', 15, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 10:01:13', '2021-08-22 10:01:13', '2022-08-22 10:01:13'),
('bc99112eb40740f7c95090a354a7b96f7b7a0ad87f5228bf84667c3a5538139335bf72cd96a641c3', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:53:19', '2022-01-11 13:53:19', '2023-01-11 13:53:19'),
('bf2e4b78f5521bf1828da246b5cc2b3c64c5f921aca70e3ad98010cdcbbb9fdce613f9b008a14330', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:47:01', '2022-01-11 12:47:01', '2023-01-11 12:47:01'),
('c2453a6517030b178385f5446f47445c2b702a0ea9e22aae5fc83ac9857c17b14de1aff2d5df0281', 3, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 14:05:55', '2021-08-21 14:05:55', '2022-08-21 14:05:55'),
('c2a154723e08633014f12f41c5436a794f83a51ce4c628e5abe7a92e4d9ae59a3527b870654a4a1d', 17, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:09:49', '2021-10-17 00:09:49', '2022-10-17 00:09:49'),
('c57ac29834b7ecff121e1069bcf02a7b31d9c000bc1ef3707d19ef0bde5738ec33b01ee237aaf35e', 3, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 13:27:20', '2021-08-21 13:27:20', '2022-08-21 13:27:20'),
('c693c45cae79370a4f96b13ab7f7f28fdbd0448a839c29926e7bdc7ca81534f203620bd271b1ef08', 18, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 00:19:47', '2021-10-17 00:19:47', '2022-10-17 00:19:47'),
('c89dcf114e83e45310d3ffd40815cf82ba8f21966e897827bcbb8171757795d1293ad066647787bc', 25, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-11-15 16:14:24', '2021-11-15 16:14:24', '2022-11-15 16:14:24'),
('cbc2b22946f9ee7ccf832963139d44ebc90cd1b47774d832f0d4b149d2d9f76ec07b68d2c50a7cbc', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 20:49:56', '2021-08-21 20:49:56', '2022-08-21 20:49:56'),
('cc3d0fe054aab9cbd712df3aabb629953cf53608a2bf4c70b9e74161252ffbdc34d74cae93d4bf29', 22, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 15:37:03', '2021-10-17 15:37:03', '2022-10-17 15:37:03'),
('cddf2b6b74d2622e5ba736fdd45563dc9b7bf2de0e5aafb908210e920eedc12a182eccdf0164c2ae', 7, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:39:53', '2021-08-21 22:39:53', '2022-08-21 22:39:53'),
('ce09eebb3b0e9fffb141cc539fd69868ee6a65e28d542bec21274af721686b2bb022650ec2373fb4', 42, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:17:08', '2022-01-20 18:17:08', '2023-01-20 18:17:08'),
('cf20f66a1eb30284f8f80188b5fc4e22e90ef912922c63cd133bdddb95d3f05ee3e6ecf3a0b1d429', 37, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 14:15:42', '2022-01-11 14:15:42', '2023-01-11 14:15:42'),
('d27d37091db512ac11d9e3d3094c466b21c0428f82b24b7fd3d7a0612075504662673ee9e71de965', 38, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-20 18:14:17', '2022-01-20 18:14:17', '2023-01-20 18:14:17'),
('d5d3668001e81954d70480366eebab6ce7a0b1f30efaf1a46ae9ffcc0c86d8469ebeee1cfdb4f185', 1, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:39:48', '2022-05-22 12:39:48', '2023-05-22 17:39:48'),
('d80ecf6ef5801c9dccf09e60ffb131879a36842cd5925bcd846c02eeae37e1b0f07c1ad4f646be8d', 3, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:37:45', '2021-08-21 21:37:45', '2022-08-21 21:37:45'),
('d8e7a31178ec263b8def0c95f1efd8682386c8a509539fd8e5410e754f106c42db38b871b2988d5b', 13, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 02:09:31', '2021-08-22 02:09:31', '2022-08-22 02:09:31'),
('da3986cf53487710ff021d7b394ff231c7628fb382cb5951aec0f241e285bdfa5c2f5596b23040af', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 01:26:31', '2021-08-22 01:26:31', '2022-08-22 01:26:31'),
('db7e90ed4e8dee99348eb8866249747fd3356d6a6f4485e808954bda0d06209414d504fec8c9f710', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:21:54', '2021-08-21 22:21:54', '2022-08-21 22:21:54'),
('dc094a353d29f070b757b86196760661d5ef92ed843c7f6173d9479e6af6ad989cac268a8f457c5f', 1, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:40:28', '2022-05-22 12:40:28', '2023-05-22 17:40:28'),
('dd680d7280a92ed2c7a482ca40999f4d03194e1d79b1de7c7432fcad2d1f85b7c16af9419fcea7bc', 6, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:38:59', '2021-08-21 22:38:59', '2022-08-21 22:38:59'),
('e01fb326eba4af2e07f5e3396836f8acd031b32eb82ae1965480b23a9ffea1b92a6e56093bf48d5f', 26, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-12-05 18:46:02', '2021-12-05 18:46:02', '2022-12-05 18:46:02'),
('e0fec803e4701be490d35e620bd1b46dd6f2e115215f296dc22555c3a5d096aeeddab0266f01e1ef', 6, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:18:02', '2021-08-21 22:18:02', '2022-08-21 22:18:02'),
('e6bb2365ff625b16f6f250ce7b4ab2764839b2800974ef7fd0796febd7bff34da9af2825517f2f2a', 2, 3, 'RestaurantCustomerAuth', '[]', 0, '2022-05-22 12:48:35', '2022-05-22 12:48:35', '2023-05-22 17:48:35'),
('ea05b0290f63ebbf44913bf19bb31d76ad805d80a8fec2ebe86cd97830d060810a57d336de4ab2e7', 10, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 21:59:36', '2021-08-21 21:59:36', '2022-08-21 21:59:36'),
('f003b0f52e33b9ccbaa512de3c07811a42812ea5dbe25e83828972ec25a743e7af065456d2909e5d', 29, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 12:19:50', '2022-01-11 12:19:50', '2023-01-11 12:19:50'),
('f797a82f4cb2f4b9c04f03336bfde2d2ad03b6afd3b18c6a6c3523dcafc2da7a9ec7c0235323c0b2', 33, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:11:08', '2022-01-11 13:11:08', '2023-01-11 13:11:08'),
('f7a698643c8b75889276ca77be419c04490fe5fb688cd2033a48abf4f369802d73d06e1b3a88d488', 23, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-10-17 15:53:18', '2021-10-17 15:53:18', '2022-10-17 15:53:18'),
('f7fcc0694d8ea2d8a8675525eabb63555450dbb5037a675d24ac4140ccd64790d03d658b83f308df', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2022-01-11 13:37:39', '2022-01-11 13:37:39', '2023-01-11 13:37:39'),
('f8fb6655ace04f362e6d0f111fe418e48d01a76fc72326d1cfc0b0a2722055cd2ef8f0bacee161a5', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:13:02', '2021-08-21 22:13:02', '2022-08-21 22:13:02'),
('fde3ecedd5cc64e80dcd968eaec053ccf5369b3e9e289b38fcd6c4a7ea8f94d2a7d40708ac23ddcc', 5, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:11:22', '2021-08-21 22:11:22', '2022-08-21 22:11:22'),
('fe88d34c47e8865a5ce7bf28eda5b9ff1f3c062c62eed5d5019a881f49f17f507df010447e76ae30', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-21 22:17:46', '2021-08-21 22:17:46', '2022-08-21 22:17:46'),
('feac826808c7dfc1bb5528fe48276689efce75d104c5bbfaa5876a8dc94de9869bd9bdf8ea1a25f9', 9, 1, 'RestaurantCustomerAuth', '[]', 0, '2021-08-22 19:03:32', '2021-08-22 19:03:32', '2022-08-22 19:03:32');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'qBN0j6SW6nIf47748tgxaKxnqKqCacTxa6gii8yc', NULL, 'http://localhost', 1, 0, 0, '2021-08-19 20:44:50', '2021-08-19 20:44:50'),
(2, NULL, 'Laravel Password Grant Client', 'oqQ90HOU0egjgQ01LRzHo9rADUavq16jzWm1TrjT', 'users', 'http://localhost', 0, 1, 0, '2021-08-19 20:44:50', '2021-08-19 20:44:50'),
(3, NULL, 'Laravel Personal Access Client', '6z9fra3ExhanFWt9e0z6Cnr2UjemvHANZdWnkHUy', NULL, 'http://localhost', 1, 0, 0, '2022-05-22 12:25:16', '2022-05-22 12:25:16'),
(4, NULL, 'Laravel Password Grant Client', 'DjKvX6mB03hzaccxQH7dMxq4s2MMn7rsqtqW9yah', 'users', 'http://localhost', 0, 1, 0, '2022-05-22 12:25:16', '2022-05-22 12:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2021-08-19 20:44:50', '2021-08-19 20:44:50'),
(2, 3, '2022-05-22 12:25:16', '2022-05-22 12:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_amount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `coupon_discount_amount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `coupon_discount_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_tax_amount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_reference` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address_id` bigint(20) DEFAULT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `coupon_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `checked` tinyint(1) NOT NULL DEFAULT 0,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_charge` decimal(24,2) NOT NULL DEFAULT 0.00,
  `schedule_at` timestamp NULL DEFAULT NULL,
  `callback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pending` timestamp NULL DEFAULT NULL,
  `accepted` timestamp NULL DEFAULT NULL,
  `confirmed` timestamp NULL DEFAULT NULL,
  `processing` timestamp NULL DEFAULT NULL,
  `handover` timestamp NULL DEFAULT NULL,
  `picked_up` timestamp NULL DEFAULT NULL,
  `delivered` timestamp NULL DEFAULT NULL,
  `canceled` timestamp NULL DEFAULT NULL,
  `refund_requested` timestamp NULL DEFAULT NULL,
  `refunded` timestamp NULL DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled` tinyint(1) NOT NULL DEFAULT 0,
  `restaurant_discount_amount` decimal(24,2) NOT NULL,
  `original_delivery_charge` decimal(24,2) NOT NULL DEFAULT 0.00,
  `failed` timestamp NULL DEFAULT NULL,
  `adjusment` decimal(24,2) NOT NULL DEFAULT 0.00,
  `edited` tinyint(1) NOT NULL DEFAULT 0,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_delivery_histories`
--

CREATE TABLE `order_delivery_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `start_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `food_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `price` decimal(24,2) NOT NULL DEFAULT 0.00,
  `food_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `add_ons` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_on_food` decimal(24,2) DEFAULT NULL,
  `discount_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'amount',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `tax_amount` decimal(24,2) NOT NULL DEFAULT 1.00,
  `variant` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `item_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_add_on_price` decimal(24,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_transactions`
--

CREATE TABLE `order_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `order_amount` decimal(24,2) NOT NULL,
  `restaurant_amount` decimal(24,2) NOT NULL,
  `admin_commission` decimal(24,2) NOT NULL,
  `received_by` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `delivery_charge` decimal(24,2) NOT NULL DEFAULT 0.00,
  `original_delivery_charge` decimal(24,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(24,2) NOT NULL DEFAULT 0.00,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phone_verifications`
--

CREATE TABLE `phone_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phone_verifications`
--

INSERT INTO `phone_verifications` (`id`, `phone`, `token`, `created_at`, `updated_at`) VALUES
(3, '+8801879762951', '3136', '2021-08-21 22:13:02', '2021-08-21 22:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `provide_d_m_earnings`
--

CREATE TABLE `provide_d_m_earnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_man_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(24,2) NOT NULL DEFAULT 0.00,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `footer_text` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `minimum_order` decimal(24,2) NOT NULL DEFAULT 0.00,
  `comission` decimal(24,2) DEFAULT NULL,
  `schedule_order` tinyint(1) NOT NULL DEFAULT 0,
  `opening_time` time DEFAULT '10:00:00',
  `closeing_time` time DEFAULT '23:00:00',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `free_delivery` tinyint(1) NOT NULL DEFAULT 0,
  `rating` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery` tinyint(1) NOT NULL DEFAULT 1,
  `take_away` tinyint(1) NOT NULL DEFAULT 1,
  `food_section` tinyint(1) NOT NULL DEFAULT 1,
  `tax` decimal(24,2) NOT NULL DEFAULT 0.00,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reviews_section` tinyint(1) NOT NULL DEFAULT 1,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `off_day` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ' ',
  `gst` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `self_delivery_system` tinyint(1) NOT NULL DEFAULT 0,
  `pos_system` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_charge` decimal(24,2) NOT NULL DEFAULT 0.00,
  `delivery_time` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '30-40',
  `veg` tinyint(1) NOT NULL DEFAULT 1,
  `non_veg` tinyint(1) NOT NULL DEFAULT 1,
  `order_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_order` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `phone`, `email`, `logo`, `latitude`, `longitude`, `address`, `footer_text`, `minimum_order`, `comission`, `schedule_order`, `opening_time`, `closeing_time`, `status`, `vendor_id`, `created_at`, `updated_at`, `free_delivery`, `rating`, `cover_photo`, `delivery`, `take_away`, `food_section`, `tax`, `zone_id`, `reviews_section`, `active`, `off_day`, `gst`, `self_delivery_system`, `pos_system`, `delivery_charge`, `delivery_time`, `veg`, `non_veg`, `order_count`, `total_order`) VALUES
(1, 'KFC Pakistan', '923002244551', 'kfc@gmail.com', '2022-05-22-628a2f4f54158.png', '24.883408219591622', '67.1472828541091', 'main Shah faisal colony, Karachi, Pakistan', NULL, '0.00', NULL, 0, '00:00:00', '23:59:00', 1, 1, '2022-05-22 12:40:47', '2022-05-22 12:50:06', 0, NULL, '2022-05-22-628a2f4f564ca.png', 1, 1, 1, '5.00', 2, 1, 1, '', '{\"status\":null,\"code\":null}', 0, 0, '0.00', '30-40', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_wallets`
--

CREATE TABLE `restaurant_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `total_earning` decimal(24,2) NOT NULL DEFAULT 0.00,
  `total_withdrawn` decimal(24,2) NOT NULL DEFAULT 0.00,
  `pending_withdraw` decimal(24,2) NOT NULL DEFAULT 0.00,
  `collected_cash` decimal(24,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_zone`
--

CREATE TABLE `restaurant_zone` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `zone_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `food_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comment` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `item_campaign_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soft_credentials`
--

CREATE TABLE `soft_credentials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `track_deliverymen`
--

CREATE TABLE `track_deliverymen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `longitude` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `translationable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translationable_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `f_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_phone_verified` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cm_firebase_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `order_count` int(11) NOT NULL DEFAULT 0,
  `login_medium` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `f_name`, `l_name`, `phone`, `email`, `image`, `is_phone_verified`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `interest`, `cm_firebase_token`, `status`, `order_count`, `login_medium`, `social_id`, `zone_id`) VALUES
(1, 'Adnan', 'Arshad', '+923333939878', 'adnan@gmail.com', NULL, 0, NULL, '$2y$10$e5Gxq4YeiNHRr/ez.knNQu0tO02leuFQx5DeOUERueBpnhmW0nB3O', NULL, '2022-05-22 12:39:48', '2022-05-22 12:53:42', NULL, 'eJJA6H1JSIOVJOnB6YuHNP:APA91bHNyDO2tLifyaeoDTVPNdEBfFDPfokuLhlsDmG3EKujLM0p-d2k90Jd65rHm4Ia8STzp2fuIFJ3d2YZM3hJz6SdpgCbyTl0EBEj8zr3zj4iwsjY0fx1lQxjduTZ5Cnf8TDFwJbS', 1, 0, NULL, NULL, 2),
(2, 'Ibrahim', 'Ahmed', '+92300822800', 'ibrahim@gmail.com', NULL, 0, NULL, '$2y$10$5Jmgz/iPZvS6zO63JxPlv.dIjOLrGE9Tj6xSIu7qq.X7DE1yIc4qm', NULL, '2022-05-22 12:48:35', '2022-05-22 12:48:35', NULL, '@', 1, 0, NULL, NULL, NULL),
(3, 'Adnan', 'Arshad', '+9203333939878', 'adnan1@gmail.com', NULL, 0, NULL, '$2y$10$k/uovORhzb4Ox5tYfVyNc.N8lp/9sfkGaOOk3mwZQxEpy8di2wviS', NULL, '2022-05-22 12:52:38', '2022-05-22 12:52:38', NULL, '@', 1, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_man_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `f_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `l_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holder_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `firebase_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `f_name`, `l_name`, `phone`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `bank_name`, `branch`, `holder_name`, `account_no`, `image`, `status`, `firebase_token`, `auth_token`) VALUES
(1, 'Maria', 'Maria', '923002244551', 'kfc@gmail.com', NULL, '$2y$10$8/48cOYgf.JctZw6Mx6gCuLe03OUmpbwW8XHNSzpw1sSdEzbzqM6S', 'bwcgV71WsRQpRFPavienW3bTgtj65frRTNgMyTQ4e80pU9aBkw3EcWSi0wPW', '2022-05-22 12:40:47', '2022-05-22 12:40:47', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_employees`
--

CREATE TABLE `vendor_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `f_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_role_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `restaurant_id` bigint(20) UNSIGNED NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firebase_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `food_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `restaurant_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdraw_requests`
--

CREATE TABLE `withdraw_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `coordinates` polygon NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `restaurant_wise_topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_wise_topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deliveryman_wise_topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `coordinates`, `status`, `created_at`, `updated_at`, `restaurant_wise_topic`, `customer_wise_topic`, `deliveryman_wise_topic`) VALUES
(1, 'Shah Faisal Town Zone', 0x00000000010300000001000000060000006bcfbac607ca50400c296e4b8fe438406dcfbab6e8cb50403a653d17f7e2384071cfba76b0cb50409c5623bc0fde38405bcfbab645c950406b9168c36adf384055cfba0605c950401fd2f25f9ae338406bcfbac607ca50400c296e4b8fe43840, 1, '2022-05-22 12:38:15', '2022-05-22 12:38:15', 'zone_1_restaurant', 'zone_1_customer', 'zone_1_delivery_man'),
(2, 'Karachi Zone', 0x0000000001030000000100000006000000db9cb62b5fc150406114640e663a3a40db9cb62b43195140d99ec454b9653940db9cb62bafda5040677cea5bcdc73840db9cb62bdfaa5040a561a08dc6de3840db9cb62b23a3504010ed5cb61aa03940db9cb62b5fc150406114640e663a3a40, 1, '2022-05-22 12:49:22', '2022-05-22 12:49:22', 'zone_2_restaurant', 'zone_2_customer', 'zone_2_delivery_man');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `add_ons`
--
ALTER TABLE `add_ons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_wallets`
--
ALTER TABLE `admin_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `business_settings`
--
ALTER TABLE `business_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coupons_code_unique` (`code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_histories`
--
ALTER TABLE `delivery_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_man_wallets`
--
ALTER TABLE `delivery_man_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_men`
--
ALTER TABLE `delivery_men`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_men_phone_unique` (`phone`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `d_m_reviews`
--
ALTER TABLE `d_m_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_roles`
--
ALTER TABLE `employee_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `food`
--
ALTER TABLE `food`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_campaigns`
--
ALTER TABLE `item_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_configs`
--
ALTER TABLE `mail_configs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_delivery_histories`
--
ALTER TABLE `order_delivery_histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_transactions`
--
ALTER TABLE `order_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_transactions_zone_id_index` (`zone_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_verifications_phone_unique` (`phone`);

--
-- Indexes for table `provide_d_m_earnings`
--
ALTER TABLE `provide_d_m_earnings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `restaurants_phone_unique` (`phone`);

--
-- Indexes for table `restaurant_wallets`
--
ALTER TABLE `restaurant_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_zone`
--
ALTER TABLE `restaurant_zone`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soft_credentials`
--
ALTER TABLE `soft_credentials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `track_deliverymen`
--
ALTER TABLE `track_deliverymen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translations_translationable_id_index` (`translationable_id`),
  ADD KEY `translations_locale_index` (`locale`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_zone_id_index` (`zone_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendors_phone_unique` (`phone`),
  ADD UNIQUE KEY `vendors_email_unique` (`email`);

--
-- Indexes for table `vendor_employees`
--
ALTER TABLE `vendor_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vendor_employees_email_unique` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zones_name_unique` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `add_ons`
--
ALTER TABLE `add_ons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_wallets`
--
ALTER TABLE `admin_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business_settings`
--
ALTER TABLE `business_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_histories`
--
ALTER TABLE `delivery_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_man_wallets`
--
ALTER TABLE `delivery_man_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_men`
--
ALTER TABLE `delivery_men`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `d_m_reviews`
--
ALTER TABLE `d_m_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_roles`
--
ALTER TABLE `employee_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food`
--
ALTER TABLE `food`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `item_campaigns`
--
ALTER TABLE `item_campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mail_configs`
--
ALTER TABLE `mail_configs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_delivery_histories`
--
ALTER TABLE `order_delivery_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_transactions`
--
ALTER TABLE `order_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `provide_d_m_earnings`
--
ALTER TABLE `provide_d_m_earnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `restaurant_wallets`
--
ALTER TABLE `restaurant_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_zone`
--
ALTER TABLE `restaurant_zone`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soft_credentials`
--
ALTER TABLE `soft_credentials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `track_deliverymen`
--
ALTER TABLE `track_deliverymen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_employees`
--
ALTER TABLE `vendor_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `withdraw_requests`
--
ALTER TABLE `withdraw_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
