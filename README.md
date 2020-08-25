<h1 align="center">Admin Plugin for <a href="https://flextype.org/">Flextype</a></h1>

![preview](https://github.com/flextype-plugins/admin/raw/dev/preview.png)

<p align="center">
<a href="https://github.com/flextype-plugins/admin/releases"><img alt="Version" src="https://img.shields.io/github/release/flextype-plugins/admin.svg?label=version&color=black"></a> <a href="https://github.com/flextype-plugins/admin"><img src="https://img.shields.io/badge/license-MIT-blue.svg?color=black" alt="License"></a> <a href="https://github.com/flextype-plugins/admin"><img src="https://img.shields.io/github/downloads/flextype-plugins/admin/total.svg?color=black" alt="Total downloads"></a> <a href="https://github.com/flextype/flextype"><img src="https://img.shields.io/badge/Flextype-0.9.11-green.svg?color=black" alt="Flextype"></a> <a href="https://crowdin.com/project/flextype-plugin-admin"><img src="https://d322cqt584bo4o.cloudfront.net/flextype-plugin-admin/localized.svg?color=black" alt="Crowdin"></a> <a href="https://scrutinizer-ci.com/g/flextype-plugins/admin?branch=dev&color=black"><img src="https://img.shields.io/scrutinizer/g/flextype-plugins/admin.svg?branch=dev&color=black" alt="Quality Score"></a> <a href=""><img src="https://img.shields.io/discord/423097982498635778.svg?logo=discord&colorB=728ADA&label=Discord%20Chat" alt="Discord"></a>
</p>

Admin Panel plugin for Flextype.

## Dependencies

The following dependencies need to be installed for Form Admin Plugin.

| Item | Version | Download |
|---|---|---|
| [flextype](https://github.com/flextype/flextype) | 0.9.11 | [download](https://github.com/flextype/flextype/releases) |
| [twig](https://github.com/flextype-plugins/twig) | >=1.0.0 | [download](https://github.com/flextype-plugins/twig/releases) |
| [form](https://github.com/flextype-plugins/form) | >=1.0.0 | [download](https://github.com/flextype-plugins/form/releases) |
| [form-admin](https://github.com/flextype-plugins/form-admin) | >=1.0.0 | [download](https://github.com/flextype-plugins/form-admin/releases) |
| [jquery](https://github.com/flextype-plugins/jquery) | >=1.0.0 | [download](https://github.com/flextype-plugins/jquery/releases) |
| [icon](https://github.com/flextype-plugins/icon) | >=1.0.0 | [download](https://github.com/flextype-plugins/icon/releases) |
| [acl](https://github.com/flextype-plugins/acl) | >=1.0.0 | [download](https://github.com/flextype-plugins/acl/releases) |
| [accounts-admin](https://github.com/flextype-plugins/accounts-admin) | >=1.0.0 | [download](https://github.com/flextype-plugins/accounts-admin/releases) |
| [phpmailer](https://github.com/flextype-plugins/phpmailer) | >=1.0.0 | [download](https://github.com/flextype-plugins/phpmailer/releases) |

## Installation

1. Download & Install all required dependencies.
2. Create new folder `/project/plugins/admin`
3. Download Admin Plugin and unzip plugin content to the folder `/project/plugins/admin`
4. **Go to `YOUR_SITE_URL/admin/accounts/registration` and create your super admin account.**

## Settings

| Key | Value | Description |
|---|---|---|
| enabled | true | true or false to disable the plugin |
| priority | 80 | admin plugin priority |
| flextype_menu | [] | admin flextype menu |
| route | admin | custom admin panel route |


### Entries settings
```
entries:
  items_view_default: list
  slugify: true
  media:
    upload_images_quality: 70
    upload_images_width: 1600
    upload_images_height: 0
    accept_file_types: gif, jpg, jpeg, png, ico, zip, tgz, txt, md, doc, docx, pdf, epub, xls, xlsx, ppt, pptx, mp3, ogg, wav, m4a, mp4, m4v, ogv, wmv, avi, webm, svg
```

## LICENSE
[The MIT License (MIT)](https://github.com/flextype-plugins/admin/blob/master/LICENSE.txt)
Copyright (c) 2018-2020 [Sergey Romanenko](https://github.com/Awilum)
