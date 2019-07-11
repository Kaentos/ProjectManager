-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 07-Jul-2019 às 16:14
-- Versão do servidor: 10.1.39-MariaDB
-- versão do PHP: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmanager`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `TwoChar` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ThreeChar` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `countries`
--

INSERT INTO `countries` (`id`, `name`, `TwoChar`, `ThreeChar`) VALUES
(1, 'Afghanistan', 'AF', 'AFG'),
(2, 'Aland Islands', 'AX', 'ALA'),
(3, 'Albania', 'AL', 'ALB'),
(4, 'Algeria', 'DZ', 'DZA'),
(5, 'American Samoa', 'AS', 'ASM'),
(6, 'Andorra', 'AD', 'AND'),
(7, 'Angola', 'AO', 'AGO'),
(8, 'Anguilla', 'AI', 'AIA'),
(9, 'Antarctica', 'AQ', 'ATA'),
(10, 'Antigua and Barbuda', 'AG', 'ATG'),
(11, 'Argentina', 'AR', 'ARG'),
(12, 'Armenia', 'AM', 'ARM'),
(13, 'Aruba', 'AW', 'ABW'),
(14, 'Australia', 'AU', 'AUS'),
(15, 'Austria', 'AT', 'AUT'),
(16, 'Azerbaijan', 'AZ', 'AZE'),
(17, 'Bahamas', 'BS', 'BHS'),
(18, 'Bahrain', 'BH', 'BHR'),
(19, 'Bangladesh', 'BD', 'BGD'),
(20, 'Barbados', 'BB', 'BRB'),
(21, 'Belarus', 'BY', 'BLR'),
(22, 'Belgium', 'BE', 'BEL'),
(23, 'Belize', 'BZ', 'BLZ'),
(24, 'Benin', 'BJ', 'BEN'),
(25, 'Bermuda', 'BM', 'BMU'),
(26, 'Bhutan', 'BT', 'BTN'),
(27, 'Bolivia', 'BO', 'BOL'),
(28, 'Bonaire, Sint Eustatius a', 'BQ', 'BES'),
(29, 'Bosnia and Herzegovina', 'BA', 'BIH'),
(30, 'Botswana', 'BW', 'BWA'),
(31, 'Bouvet Island', 'BV', 'BVT'),
(32, 'Brazil', 'BR', 'BRA'),
(33, 'British Indian Ocean Terr', 'IO', 'IOT'),
(34, 'Brunei', 'BN', 'BRN'),
(35, 'Bulgaria', 'BG', 'BGR'),
(36, 'Burkina Faso', 'BF', 'BFA'),
(37, 'Burundi', 'BI', 'BDI'),
(38, 'Cambodia', 'KH', 'KHM'),
(39, 'Cameroon', 'CM', 'CMR'),
(40, 'Canada', 'CA', 'CAN'),
(41, 'Cape Verde', 'CV', 'CPV'),
(42, 'Cayman Islands', 'KY', 'CYM'),
(43, 'Central African Republic', 'CF', 'CAF'),
(44, 'Chad', 'TD', 'TCD'),
(45, 'Chile', 'CL', 'CHL'),
(46, 'China', 'CN', 'CHN'),
(47, 'Christmas Island', 'CX', 'CXR'),
(48, 'Cocos (Keeling) Islands', 'CC', 'CCK'),
(49, 'Colombia', 'CO', 'COL'),
(50, 'Comoros', 'KM', 'COM'),
(51, 'Congo', 'CG', 'COG'),
(52, 'Cook Islands', 'CK', 'COK'),
(53, 'Costa Rica', 'CR', 'CRI'),
(54, 'Ivory Coast', 'CI', 'CIV'),
(55, 'Croatia', 'HR', 'HRV'),
(56, 'Cuba', 'CU', 'CUB'),
(57, 'Curacao', 'CW', 'CUW'),
(58, 'Cyprus', 'CY', 'CYP'),
(59, 'Czech Republic', 'CZ', 'CZE'),
(60, 'Democratic Republic of th', 'CD', 'COD'),
(61, 'Denmark', 'DK', 'DNK'),
(62, 'Djibouti', 'DJ', 'DJI'),
(63, 'Dominica', 'DM', 'DMA'),
(64, 'Dominican Republic', 'DO', 'DOM'),
(65, 'Ecuador', 'EC', 'ECU'),
(66, 'Egypt', 'EG', 'EGY'),
(67, 'El Salvador', 'SV', 'SLV'),
(68, 'Equatorial Guinea', 'GQ', 'GNQ'),
(69, 'Eritrea', 'ER', 'ERI'),
(70, 'Estonia', 'EE', 'EST'),
(71, 'Ethiopia', 'ET', 'ETH'),
(72, 'Falkland Islands (Malvina', 'FK', 'FLK'),
(73, 'Faroe Islands', 'FO', 'FRO'),
(74, 'Fiji', 'FJ', 'FJI'),
(75, 'Finland', 'FI', 'FIN'),
(76, 'France', 'FR', 'FRA'),
(77, 'French Guiana', 'GF', 'GUF'),
(78, 'French Polynesia', 'PF', 'PYF'),
(79, 'French Southern Territori', 'TF', 'ATF'),
(80, 'Gabon', 'GA', 'GAB'),
(81, 'Gambia', 'GM', 'GMB'),
(82, 'Georgia', 'GE', 'GEO'),
(83, 'Germany', 'DE', 'DEU'),
(84, 'Ghana', 'GH', 'GHA'),
(85, 'Gibraltar', 'GI', 'GIB'),
(86, 'Greece', 'GR', 'GRC'),
(87, 'Greenland', 'GL', 'GRL'),
(88, 'Grenada', 'GD', 'GRD'),
(89, 'Guadaloupe', 'GP', 'GLP'),
(90, 'Guam', 'GU', 'GUM'),
(91, 'Guatemala', 'GT', 'GTM'),
(92, 'Guernsey', 'GG', 'GGY'),
(93, 'Guinea', 'GN', 'GIN'),
(94, 'Guinea-Bissau', 'GW', 'GNB'),
(95, 'Guyana', 'GY', 'GUY'),
(96, 'Haiti', 'HT', 'HTI'),
(97, 'Heard Island and McDonald', 'HM', 'HMD'),
(98, 'Honduras', 'HN', 'HND'),
(99, 'Hong Kong', 'HK', 'HKG'),
(100, 'Hungary', 'HU', 'HUN'),
(101, 'Iceland', 'IS', 'ISL'),
(102, 'India', 'IN', 'IND'),
(103, 'Indonesia', 'ID', 'IDN'),
(104, 'Iran', 'IR', 'IRN'),
(105, 'Iraq', 'IQ', 'IRQ'),
(106, 'Ireland', 'IE', 'IRL'),
(107, 'Isle of Man', 'IM', 'IMN'),
(108, 'Israel', 'IL', 'ISR'),
(109, 'Italy', 'IT', 'ITA'),
(110, 'Jamaica', 'JM', 'JAM'),
(111, 'Japan', 'JP', 'JPN'),
(112, 'Jersey', 'JE', 'JEY'),
(113, 'Jordan', 'JO', 'JOR'),
(114, 'Kazakhstan', 'KZ', 'KAZ'),
(115, 'Kenya', 'KE', 'KEN'),
(116, 'Kiribati', 'KI', 'KIR'),
(117, 'Kosovo', 'XK', '---'),
(118, 'Kuwait', 'KW', 'KWT'),
(119, 'Kyrgyzstan', 'KG', 'KGZ'),
(120, 'Laos', 'LA', 'LAO'),
(121, 'Latvia', 'LV', 'LVA'),
(122, 'Lebanon', 'LB', 'LBN'),
(123, 'Lesotho', 'LS', 'LSO'),
(124, 'Liberia', 'LR', 'LBR'),
(125, 'Libya', 'LY', 'LBY'),
(126, 'Liechtenstein', 'LI', 'LIE'),
(127, 'Lithuania', 'LT', 'LTU'),
(128, 'Luxembourg', 'LU', 'LUX'),
(129, 'Macao', 'MO', 'MAC'),
(130, 'Macedonia', 'MK', 'MKD'),
(131, 'Madagascar', 'MG', 'MDG'),
(132, 'Malawi', 'MW', 'MWI'),
(133, 'Malaysia', 'MY', 'MYS'),
(134, 'Maldives', 'MV', 'MDV'),
(135, 'Mali', 'ML', 'MLI'),
(136, 'Malta', 'MT', 'MLT'),
(137, 'Marshall Islands', 'MH', 'MHL'),
(138, 'Martinique', 'MQ', 'MTQ'),
(139, 'Mauritania', 'MR', 'MRT'),
(140, 'Mauritius', 'MU', 'MUS'),
(141, 'Mayotte', 'YT', 'MYT'),
(142, 'Mexico', 'MX', 'MEX'),
(143, 'Micronesia', 'FM', 'FSM'),
(144, 'Moldava', 'MD', 'MDA'),
(145, 'Monaco', 'MC', 'MCO'),
(146, 'Mongolia', 'MN', 'MNG'),
(147, 'Montenegro', 'ME', 'MNE'),
(148, 'Montserrat', 'MS', 'MSR'),
(149, 'Morocco', 'MA', 'MAR'),
(150, 'Mozambique', 'MZ', 'MOZ'),
(151, 'Myanmar (Burma)', 'MM', 'MMR'),
(152, 'Namibia', 'NA', 'NAM'),
(153, 'Nauru', 'NR', 'NRU'),
(154, 'Nepal', 'NP', 'NPL'),
(155, 'Netherlands', 'NL', 'NLD'),
(156, 'New Caledonia', 'NC', 'NCL'),
(157, 'New Zealand', 'NZ', 'NZL'),
(158, 'Nicaragua', 'NI', 'NIC'),
(159, 'Niger', 'NE', 'NER'),
(160, 'Nigeria', 'NG', 'NGA'),
(161, 'Niue', 'NU', 'NIU'),
(162, 'Norfolk Island', 'NF', 'NFK'),
(163, 'North Korea', 'KP', 'PRK'),
(164, 'Northern Mariana Islands', 'MP', 'MNP'),
(165, 'Norway', 'NO', 'NOR'),
(166, 'Oman', 'OM', 'OMN'),
(167, 'Pakistan', 'PK', 'PAK'),
(168, 'Palau', 'PW', 'PLW'),
(169, 'Palestine', 'PS', 'PSE'),
(170, 'Panama', 'PA', 'PAN'),
(171, 'Papua New Guinea', 'PG', 'PNG'),
(172, 'Paraguay', 'PY', 'PRY'),
(173, 'Peru', 'PE', 'PER'),
(174, 'Phillipines', 'PH', 'PHL'),
(175, 'Pitcairn', 'PN', 'PCN'),
(176, 'Poland', 'PL', 'POL'),
(177, 'Portugal', 'PT', 'PRT'),
(178, 'Puerto Rico', 'PR', 'PRI'),
(179, 'Qatar', 'QA', 'QAT'),
(180, 'Reunion', 'RE', 'REU'),
(181, 'Romania', 'RO', 'ROU'),
(182, 'Russia', 'RU', 'RUS'),
(183, 'Rwanda', 'RW', 'RWA'),
(184, 'Saint Barthelemy', 'BL', 'BLM'),
(185, 'Saint Helena', 'SH', 'SHN'),
(186, 'Saint Kitts and Nevis', 'KN', 'KNA'),
(187, 'Saint Lucia', 'LC', 'LCA'),
(188, 'Saint Martin', 'MF', 'MAF'),
(189, 'Saint Pierre and Miquelon', 'PM', 'SPM'),
(190, 'Saint Vincent and the Gre', 'VC', 'VCT'),
(191, 'Samoa', 'WS', 'WSM'),
(192, 'San Marino', 'SM', 'SMR'),
(193, 'Sao Tome and Principe', 'ST', 'STP'),
(194, 'Saudi Arabia', 'SA', 'SAU'),
(195, 'Senegal', 'SN', 'SEN'),
(196, 'Serbia', 'RS', 'SRB'),
(197, 'Seychelles', 'SC', 'SYC'),
(198, 'Sierra Leone', 'SL', 'SLE'),
(199, 'Singapore', 'SG', 'SGP'),
(200, 'Sint Maarten', 'SX', 'SXM'),
(201, 'Slovakia', 'SK', 'SVK'),
(202, 'Slovenia', 'SI', 'SVN'),
(203, 'Solomon Islands', 'SB', 'SLB'),
(204, 'Somalia', 'SO', 'SOM'),
(205, 'South Africa', 'ZA', 'ZAF'),
(206, 'South Georgia and the Sou', 'GS', 'SGS'),
(207, 'South Korea', 'KR', 'KOR'),
(208, 'South Sudan', 'SS', 'SSD'),
(209, 'Spain', 'ES', 'ESP'),
(210, 'Sri Lanka', 'LK', 'LKA'),
(211, 'Sudan', 'SD', 'SDN'),
(212, 'Suriname', 'SR', 'SUR'),
(213, 'Svalbard and Jan Mayen', 'SJ', 'SJM'),
(214, 'Swaziland', 'SZ', 'SWZ'),
(215, 'Sweden', 'SE', 'SWE'),
(216, 'Switzerland', 'CH', 'CHE'),
(217, 'Syria', 'SY', 'SYR'),
(218, 'Taiwan', 'TW', 'TWN'),
(219, 'Tajikistan', 'TJ', 'TJK'),
(220, 'Tanzania', 'TZ', 'TZA'),
(221, 'Thailand', 'TH', 'THA'),
(222, 'Timor-Leste (East Timor)', 'TL', 'TLS'),
(223, 'Togo', 'TG', 'TGO'),
(224, 'Tokelau', 'TK', 'TKL'),
(225, 'Tonga', 'TO', 'TON'),
(226, 'Trinidad and Tobago', 'TT', 'TTO'),
(227, 'Tunisia', 'TN', 'TUN'),
(228, 'Turkey', 'TR', 'TUR'),
(229, 'Turkmenistan', 'TM', 'TKM'),
(230, 'Turks and Caicos Islands', 'TC', 'TCA'),
(231, 'Tuvalu', 'TV', 'TUV'),
(232, 'Uganda', 'UG', 'UGA'),
(233, 'Ukraine', 'UA', 'UKR'),
(234, 'United Arab Emirates', 'AE', 'ARE'),
(235, 'United Kingdom', 'GB', 'GBR'),
(236, 'United States', 'US', 'USA'),
(237, 'United States Minor Outly', 'UM', 'UMI'),
(238, 'Uruguay', 'UY', 'URY'),
(239, 'Uzbekistan', 'UZ', 'UZB'),
(240, 'Vanuatu', 'VU', 'VUT'),
(241, 'Vatican City', 'VA', 'VAT'),
(242, 'Venezuela', 'VE', 'VEN'),
(243, 'Vietnam', 'VN', 'VNM'),
(244, 'Virgin Islands, British', 'VG', 'VGB'),
(245, 'Virgin Islands, US', 'VI', 'VIR'),
(246, 'Wallis and Futuna', 'WF', 'WLF'),
(247, 'Western Sahara', 'EH', 'ESH'),
(248, 'Yemen', 'YE', 'YEM'),
(249, 'Zambia', 'ZM', 'ZMB'),
(250, 'Zimbabwe', 'ZW', 'ZWE');

-- --------------------------------------------------------

--
-- Estrutura da tabela `issuecomments`
--

CREATE TABLE `issuecomments` (
  `id` int(11) NOT NULL,
  `idIssue` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `comment` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creationDate` datetime NOT NULL,
  `lastUpdateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `issuefollow`
--

CREATE TABLE `issuefollow` (
  `idIssue` int(11) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Des` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idStatus` int(11) NOT NULL,
  `idCreator` int(11) NOT NULL,
  `idUpdateUser` int(11) NOT NULL,
  `creationDate` datetime NOT NULL,
  `lastupdatedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `issues`
--

INSERT INTO `issues` (`id`, `idProject`, `name`, `Des`, `idStatus`, `idCreator`, `idUpdateUser`, `creationDate`, `lastupdatedDate`) VALUES
(4, 14, 'Problema com inputs', 'Alguns inputs do registo nÃ£o funcionam corretamente!', 1, 8, 8, '2019-07-05 22:39:32', '2019-07-05 22:39:32'),
(5, 3, 'Problema #1', 'DescriÃ§Ã£o do problema #1', 3, 8, 8, '2019-07-05 22:41:22', '2019-07-05 22:43:25'),
(6, 3, 'Problema #2', 'DescriÃ§Ã£o do problema #2\r\n', 1, 8, 8, '2019-07-05 22:41:35', '2019-07-05 22:43:07'),
(7, 3, 'Problema #3', 'DescriÃ§Ã£o do problema #3', 2, 8, 8, '2019-07-05 22:43:42', '2019-07-05 22:43:42'),
(8, 14, 'Criador pode alterar seu role', 'Proibir criador alterar o seu role', 3, 8, 8, '2019-07-05 23:53:12', '2019-07-05 23:53:12');

-- --------------------------------------------------------

--
-- Estrutura da tabela `istatus`
--

CREATE TABLE `istatus` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `istatus`
--

INSERT INTO `istatus` (`id`, `name`, `badge`) VALUES
(1, 'Solved', 'success'),
(2, 'Trying to fix', 'primary'),
(3, 'Paused', 'warning');

-- --------------------------------------------------------

--
-- Estrutura da tabela `projectmembers`
--

CREATE TABLE `projectmembers` (
  `idProject` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `idRole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `projectmembers`
--

INSERT INTO `projectmembers` (`idProject`, `idUser`, `idRole`) VALUES
(3, 8, 1),
(14, 8, 1),
(3, 11, 3),
(3, 15, 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `des` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idStatus` int(11) NOT NULL,
  `idCreator` int(11) NOT NULL,
  `creationDate` datetime NOT NULL,
  `idUpdateUser` int(11) NOT NULL,
  `lastupdatedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `projects`
--

INSERT INTO `projects` (`id`, `name`, `des`, `code`, `idStatus`, `idCreator`, `creationDate`, `idUpdateUser`, `lastupdatedDate`) VALUES
(3, 'Um titulo', 'asdsaddsa', 'eftzvzijl7e6', 3, 8, '2019-06-26 01:00:00', 13, '2019-07-05 21:54:34'),
(14, 'Project Manager', 'Projeto PAP do aluno Miguel', 'o58ry09smun6', 2, 8, '2019-07-04 12:33:13', 8, '2019-07-04 12:33:13');

-- --------------------------------------------------------

--
-- Estrutura da tabela `proles`
--

CREATE TABLE `proles` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `proles`
--

INSERT INTO `proles` (`id`, `name`, `badge`) VALUES
(1, 'Owner', 'dark'),
(2, 'Managers', 'danger'),
(3, 'Developers', 'warning'),
(4, 'Members', 'primary');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pstatus`
--

CREATE TABLE `pstatus` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `pstatus`
--

INSERT INTO `pstatus` (`id`, `name`, `badge`) VALUES
(1, 'Completed', 'success'),
(2, 'In Progress', 'primary'),
(3, 'Stopped', 'danger'),
(4, 'Cancelled', 'dark'),
(5, 'Paused', 'warning');

-- --------------------------------------------------------

--
-- Estrutura da tabela `taskcomments`
--

CREATE TABLE `taskcomments` (
  `id` int(11) NOT NULL,
  `idTask` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `comment` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creationDate` datetime NOT NULL,
  `lastUpdateDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `taskcomments`
--

INSERT INTO `taskcomments` (`id`, `idTask`, `idUser`, `comment`, `creationDate`, `lastUpdateDate`) VALUES
(3, 9, 8, 'Teste 2', '2019-07-03 23:51:31', '2019-07-03 22:03:55'),
(4, 9, 8, 'Tipo eu estou a escrever esta porra para nada pois eu nÃ£o sei o que estou a fazer da minha vida e nÃ£o sei o que vou fazer com ela.', '2019-07-03 23:51:53', NULL),
(5, 9, 11, 'Bananas', '2019-07-04 00:01:29', NULL),
(6, 7, 8, 'Bananas', '2019-07-04 00:25:27', NULL),
(7, 7, 8, 'NÃ£o sei o que dizer', '2019-07-04 00:25:33', NULL),
(8, 10, 8, 'Ya totalmente normal', '2019-07-04 00:28:35', NULL),
(9, 15, 8, 'Fazer com que apareÃ§a mensagem de erro caso o comment nÃ£o possa ser inserido', '2019-07-04 00:49:19', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `taskfollow`
--

CREATE TABLE `taskfollow` (
  `idTask` int(11) NOT NULL,
  `idUser` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `idProject` int(11) NOT NULL,
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Des` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idStatus` int(11) NOT NULL,
  `idCreator` int(11) NOT NULL,
  `idUpdateUser` int(11) NOT NULL,
  `creationDate` datetime NOT NULL,
  `lastupdatedDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tasks`
--

INSERT INTO `tasks` (`id`, `idProject`, `name`, `Des`, `idStatus`, `idCreator`, `idUpdateUser`, `creationDate`, `lastupdatedDate`) VALUES
(1, 3, 'Create table', 'CriaÃ§Ã£o de uma tabela\r\n', 1, 8, 8, '2019-06-30 01:00:00', '2019-07-05 22:49:04'),
(2, 3, 'Danger', 'Danger teste', 2, 8, 8, '2019-06-30 01:00:00', '2019-07-05 22:49:13'),
(3, 3, 'TarifÃ¡rio', 'DescriÃ§Ã£o', 4, 8, 8, '2019-06-30 02:00:00', '2019-07-05 22:48:40'),
(4, 3, 'miguel', 'miguel', 5, 8, 8, '2019-06-30 02:00:00', '2019-06-30 02:00:00'),
(5, 3, 'Tarefa #10', 'DescriÃ§Ã£o da tarefa #10', 3, 8, 8, '2019-06-30 03:00:00', '2019-07-05 22:48:09'),
(6, 3, 'extra', 'extra', 2, 8, 8, '2019-06-30 02:00:00', '2019-07-05 22:48:18'),
(7, 3, 'Tarefa #2', 'DescriÃ§Ã£o da tarefa #2', 1, 8, 8, '2019-07-02 22:32:05', '2019-07-05 22:47:50'),
(8, 3, 'Tarefa #3', 'DescriÃ§Ã£o da tarefa #', 2, 8, 8, '2019-07-02 22:35:37', '2019-07-05 22:47:43'),
(9, 3, 'Tarefa #5', 'DescriÃ§Ã£o da tarefa #5', 1, 8, 8, '2019-07-03 14:03:42', '2019-07-05 22:46:52'),
(10, 3, 'Tarefa #4', 'DescriÃ§Ã£o da tarefa #4', 1, 8, 8, '2019-07-04 00:28:25', '2019-07-05 22:47:56'),
(11, 14, 'Criar a parte das issues', 'Praticamente Ã© o mesmo que as tasks, basta copiar e criar novas tabelas com nome issue', 4, 8, 8, '2019-07-04 00:36:16', '2019-07-04 00:36:16'),
(12, 14, 'Adicionar task links', 'Adicionar \"div\" como por exemplo a tasks e issues onde apresenta sites para o github e outras cenas mais usadas.', 4, 8, 8, '2019-07-04 00:37:44', '2019-07-04 00:37:44'),
(13, 14, 'Task priority', 'Criar sistema de prioridade para as tasks', 4, 8, 8, '2019-07-04 00:38:26', '2019-07-04 00:38:26'),
(14, 14, 'Associar user a task', 'Ou seja, mostra todos os membros que estÃ£o a trabalhar nessa task', 4, 8, 8, '2019-07-04 00:39:06', '2019-07-04 00:39:06'),
(15, 14, 'Task comments', 'Possibilidade de um utilizador poder comentar na task', 3, 8, 8, '2019-07-04 00:39:49', '2019-07-04 00:39:49'),
(16, 14, 'Mostrar project members', 'Mostra todos os membros que estÃ£o associados ao projeto e os utilizadores com permissÃ£o podem remover utilizadores e alterar as suas permissÃµes', 4, 8, 8, '2019-07-04 00:40:56', '2019-07-04 00:40:56'),
(17, 14, 'Barra para projeto', 'Muda completamente a barra lateral quando estÃ¡ dentro de um projeto, para obter opÃ§Ãµes do tipo ir a todas as tasks ou issues do projeto', 4, 8, 8, '2019-07-04 00:41:54', '2019-07-04 00:41:54'),
(18, 14, 'Estilos', 'Fazer com que o site seja mais apelativo', 1, 8, 8, '2019-07-04 00:42:14', '2019-07-04 00:42:14'),
(19, 14, 'Milestones', 'Basta fazer com que aparece a lista de milestones, os dias que faltam para acabar a milestone e se foi concluida ou adiada / inacabada', 4, 8, 8, '2019-07-04 00:43:14', '2019-07-04 00:43:14'),
(20, 14, 'Adicionar novos status', 'Tanto para o projeto como para o resto', 4, 8, 8, '2019-07-04 00:43:45', '2019-07-04 00:43:45'),
(21, 14, 'User image', 'Dar possibilidade de o utilizador poder alterar a sua imagem', 4, 8, 8, '2019-07-04 00:46:03', '2019-07-04 00:46:03'),
(22, 14, 'Criar zona de bug/error report', 'Um utilizador pode reportar um erro se for redireccionado por causa de um e queira reporta-lo.', 4, 8, 8, '2019-07-04 00:47:02', '2019-07-04 00:47:02'),
(23, 14, 'Criar lista de erros', 'Conjuntamente com a task de criar zona de bug e error report, criar uma lista de erros para o admin', 4, 8, 8, '2019-07-04 00:47:33', '2019-07-04 00:47:33'),
(24, 14, 'Admin dashboard', 'PÃ¡ginas que permite o admin ver todos os dados do website', 2, 8, 8, '2019-07-04 00:48:05', '2019-07-04 00:48:05'),
(25, 14, 'Input obrigatorio', 'Referir nos inputs que Ã© obrigatÃ³rio', 4, 8, 8, '2019-07-04 00:48:37', '2019-07-04 00:48:37'),
(26, 14, 'Total etc', 'Criar uma funÃ§Ã£o que diz o total de tasks, issues etc de um certo projeto', 4, 8, 8, '2019-07-04 00:50:06', '2019-07-04 00:50:06'),
(28, 14, 'User can follow task', 'User can follow task so it appears who is assigned to that task', 3, 8, 8, '2019-07-04 17:09:47', '2019-07-04 17:36:45'),
(29, 14, 'Editar task atravÃ©s de modal', 'Fazer o mesmo que tenho para o editar user role', 4, 8, 8, '2019-07-04 17:24:58', '2019-07-04 20:52:48'),
(30, 14, 'Medidas de seguranÃ§a', 'fazer mesmo que nos membros, quando um utilizador nÃ£o autorizado tenta fazer o que nÃ£o tem acesso.', 4, 8, 8, '2019-07-04 20:53:40', '2019-07-04 20:53:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tstatus`
--

CREATE TABLE `tstatus` (
  `id` int(11) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `badge` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `tstatus`
--

INSERT INTO `tstatus` (`id`, `name`, `badge`) VALUES
(1, 'In Progress', 'primary'),
(2, 'Stopped', 'danger'),
(3, 'Completed', 'success'),
(4, 'Paused', 'warning'),
(5, 'Cancelled', 'dark');

-- --------------------------------------------------------

--
-- Estrutura da tabela `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creationDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `lastUpdateDate` datetime NOT NULL,
  `idCountry` int(11) DEFAULT NULL,
  `role` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `user`
--

INSERT INTO `user` (`id`, `email`, `username`, `creationDate`, `lastUpdateDate`, `idCountry`, `role`) VALUES
(8, 'miguel@mail.com', 'Miguel', '2019-06-20 07:33:33', '2019-06-20 07:33:33', 177, '20'),
(9, 'Example@mail.com', 'Example', '2019-06-22 07:03:26', '2019-06-22 07:03:26', 206, '0'),
(10, 'empty@mail.com', 'Empty123', '2019-06-26 11:12:10', '2019-06-26 11:12:10', 2, '0'),
(11, 'sdabudsasba@gmail.com', 'Miguel2', '2019-06-28 08:13:24', '2019-06-28 08:13:24', NULL, '0'),
(12, 'teste123@gmail.com', 'teste123', '2019-07-05 08:59:54', '2019-07-05 08:59:54', NULL, '0'),
(13, 'userdelete@deleted.deleted', 'DELETED', '2019-07-05 09:48:21', '2019-07-05 09:48:21', 177, '0'),
(15, 'membroonly@mail.com', 'Membro15', '2019-07-05 11:48:47', '2019-07-05 11:48:47', NULL, '0');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usersecurity`
--

CREATE TABLE `usersecurity` (
  `idUser` int(11) NOT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usersecurity`
--

INSERT INTO `usersecurity` (`idUser`, `password`, `question`, `answer`) VALUES
(8, '$2y$12$TZIkFUQEnw5//x9gGdKrFu45lI4D.8Rdfgrg0PG8um9DTwmxrsVZK', 'Miguel2323', '$2y$12$otBYl4M8hDegmGqpNhwf/OP5auo493lRIEK0dwMCd7zVOOR0fChZm'),
(9, '$2y$12$n4IpQ5rhDyFCgrL0W3oscOSspEj4cp6wUmPgnqS9QGbip7XBQNV.m', 'Example1.', '$2y$12$GzPs3UJM9FETmDPEGt0z1OmlaahMgSJOUCZ.OoMfnFxtvcpq2Cwuy'),
(10, '$2y$12$lbbhFepMqGCIRqdTFLPxKuVg/bFNubUS2lGEMgy9.86oMQYsu0o/C', 'Empty123', '$2y$12$UfE4Yqo1VaG2USBpzpBWnOZNpsH5e0Z0ydmYk9u98yQstXsoGwIxq'),
(11, '$2y$12$zU7lfkTFPMVEH/yWt8P5OOmhiRXiZ9B3PgG7J7AJP9RCPr85/ZJc2', 'Miguel123', '$2y$12$VsiYdHRpvKpws1ZjW.e16egyoCa1s8k72VQO8kEOODs/9F/Pr/3nS'),
(12, '$2y$12$jAmuhdbvqM/D/MC/0v2io.DOF14wT5YudFhpbXsltGQq0Q3oPmt7y', 'Testesad', '$2y$12$ZTCTDMZs1256DnWTsjKvh.u0QLkfrJZ884WGOhTh31gwj0Vd8/C7W'),
(13, '$2y$12$SUHdiI.G7Cftdrq7tpqGnuCFtFSt6MSiMfH1y6TkX089Hpsle6oNm', 'DELETED', '$2y$12$xHOZ212tAfC81Qj4YJNYw./UE.BYnz6FYxRCm.m8ON.eI/C5MlH/C'),
(15, '$2y$12$wIX9PlsQ6PKwkUPMmBcws.rvhkCS8pNNYVwZzrtCO0e4IRSKnDDum', 'Miguel1', '$2y$12$En4QK6BEtK4rRdWB.nbP1.4EKR/ZG4fRXNtZE5VpD5oFeLtEtAEk6');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issuecomments`
--
ALTER TABLE `issuecomments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idIssue` (`idIssue`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `issuefollow`
--
ALTER TABLE `issuefollow`
  ADD UNIQUE KEY `idIssue` (`idIssue`,`idUser`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idStatus` (`idStatus`),
  ADD KEY `idCreator` (`idCreator`),
  ADD KEY `idUpdateUser` (`idUpdateUser`);

--
-- Indexes for table `istatus`
--
ALTER TABLE `istatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projectmembers`
--
ALTER TABLE `projectmembers`
  ADD UNIQUE KEY `idProject` (`idProject`,`idUser`),
  ADD KEY `idUser` (`idUser`),
  ADD KEY `idRole` (`idRole`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCreator` (`idCreator`),
  ADD KEY `idStatus` (`idStatus`),
  ADD KEY `idUpdateUser` (`idUpdateUser`);

--
-- Indexes for table `proles`
--
ALTER TABLE `proles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pstatus`
--
ALTER TABLE `pstatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taskcomments`
--
ALTER TABLE `taskcomments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTask` (`idTask`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `taskfollow`
--
ALTER TABLE `taskfollow`
  ADD UNIQUE KEY `idTask` (`idTask`,`idUser`),
  ADD KEY `idUser` (`idUser`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idProject` (`idProject`),
  ADD KEY `idCreator` (`idCreator`),
  ADD KEY `lastupdateUser` (`idUpdateUser`),
  ADD KEY `idStatus` (`idStatus`);

--
-- Indexes for table `tstatus`
--
ALTER TABLE `tstatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCountry` (`idCountry`);

--
-- Indexes for table `usersecurity`
--
ALTER TABLE `usersecurity`
  ADD UNIQUE KEY `idUser` (`idUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `issuecomments`
--
ALTER TABLE `issuecomments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `istatus`
--
ALTER TABLE `istatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `proles`
--
ALTER TABLE `proles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pstatus`
--
ALTER TABLE `pstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `taskcomments`
--
ALTER TABLE `taskcomments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tstatus`
--
ALTER TABLE `tstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `issuecomments`
--
ALTER TABLE `issuecomments`
  ADD CONSTRAINT `issuecomments_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `issuecomments_ibfk_2` FOREIGN KEY (`idIssue`) REFERENCES `issues` (`id`);

--
-- Limitadores para a tabela `issuefollow`
--
ALTER TABLE `issuefollow`
  ADD CONSTRAINT `issuefollow_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `issuefollow_ibfk_2` FOREIGN KEY (`idIssue`) REFERENCES `issues` (`id`);

--
-- Limitadores para a tabela `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`idStatus`) REFERENCES `istatus` (`id`),
  ADD CONSTRAINT `issues_ibfk_2` FOREIGN KEY (`idProject`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `issues_ibfk_3` FOREIGN KEY (`idCreator`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `issues_ibfk_4` FOREIGN KEY (`idUpdateUser`) REFERENCES `user` (`id`);

--
-- Limitadores para a tabela `projectmembers`
--
ALTER TABLE `projectmembers`
  ADD CONSTRAINT `projectmembers_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `projectmembers_ibfk_2` FOREIGN KEY (`idProject`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `projectmembers_ibfk_3` FOREIGN KEY (`idRole`) REFERENCES `proles` (`id`);

--
-- Limitadores para a tabela `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`idCreator`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`idStatus`) REFERENCES `pstatus` (`id`);

--
-- Limitadores para a tabela `taskcomments`
--
ALTER TABLE `taskcomments`
  ADD CONSTRAINT `taskcomments_ibfk_1` FOREIGN KEY (`idTask`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `taskcomments_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`);

--
-- Limitadores para a tabela `taskfollow`
--
ALTER TABLE `taskfollow`
  ADD CONSTRAINT `taskfollow_ibfk_1` FOREIGN KEY (`idTask`) REFERENCES `tasks` (`id`),
  ADD CONSTRAINT `taskfollow_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`);

--
-- Limitadores para a tabela `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`idProject`) REFERENCES `projects` (`id`),
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`idCreator`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`idUpdateUser`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `tasks_ibfk_4` FOREIGN KEY (`idStatus`) REFERENCES `tstatus` (`id`);

--
-- Limitadores para a tabela `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`idCountry`) REFERENCES `countries` (`id`);

--
-- Limitadores para a tabela `usersecurity`
--
ALTER TABLE `usersecurity`
  ADD CONSTRAINT `usersecurity_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
