## Agora License Manager

Agora License Manager is an all-in-one solution for managing licenses and updates for your web applications. It allows you to control and restrict the use of your applications in accordance with your licensing agreements.

The Agora License Manager integrates seamlessly with [Agora Invoicing](https://www.agorainvoicing.com/), providing a unified platform for managing both invoicing and licensing.

Agora is developed by [Ladybird Web Solution Pvt Ltd](http://www.ladybirdweb.com/) and was launched in October 2021.

## Requirements

To run Agora License Manager, your server should meet the following requirements:

* PHP Version: 8.2+
* Laravel Framework: 10+
* Database: MySQL 8.0.x or Postgres or SQLite or SQL Server
* Web Server: Apache / IIS / Nginx
* PHP Extensions: Mcrypt, OpenSSL, Mbstring, Tokenizer
* Web Server Extension: Pretty URLs or Search Engine Friendly URLs must be enabled in your web server configuration

## Installation Guide

The installation process for Agora License Manager is identical to that of [Faveo Helpdesk](https://www.faveohelpdesk.com/). Please refer to the [Faveo Installation Guide](https://docs.faveohelpdesk.com/) for detailed instructions.

## Credits

* Laravel Framework
* Admin LTE Theme

## Features of Agora License Manager

- **Single Installation**: Manage unlimited products, clients, and licenses with one installation.
- **Flexible Licenses**: Supports lifetime, time-expiring, and feature-limited licenses.
- **Restrictions**: IP address and (sub)domain-based restrictions to enforce license limits.
- **License Enforcement**: Blocks installation for users without an active license.
- **Real-Time License Monitoring**: Direct tracking of license status and usage.
- **Piracy Protection**: Provides robust mechanisms to prevent software piracy.

## Error Reporting

Agora License Manager uses [Bugsnag](https://www.bugsnag.com/) to monitor application stability in production environments. This helps us quickly identify issues and release bug fixes and updates. 

**If you're customizing the application in a development environment**, we recommend disabling error reporting to prevent unnecessary alerts. You can easily disable it from the "Error Logs and Debugging" section in the admin panel, or by setting the environment to `development` in your `.env` file.

However, you can still report issues or share your customizations via our [GitHub Issues page](https://github.com/ladybirdweb/agora-license-manager/issues). If your changes follow our contributing guidelines, we may integrate them into the main application.

## Security Policy

### Supported Versions


| Version  | Supported            |  
|----------|----------------------|  
| 3.0.2.1  | :white_check_mark:   |  
| 3.0.2    | :x:                  |  
| < 3.0.1  | :x:                  |

### Supported Updates

Security updates are released monthly. In case of critical vulnerabilities, updates may be released twice a month.

### Reporting a Vulnerability

If you suspect a security vulnerability, please report it to [support@faveohelpdesk.com](mailto:support@faveohelpdesk.com). You will receive a response within 48 hours. If the issue is confirmed, we will aim to release a patch as quickly as possible—usually within a few days, depending on the complexity of the issue.

## User Manual

For a comprehensive user manual, visit the <a href="https://github.com/ladybirdweb/agora-license-manager/wiki" target="_blank">Agora License Manager Wiki</a>.
