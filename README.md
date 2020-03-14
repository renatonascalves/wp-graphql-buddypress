# WPGraphQL BuddyPress

Bringing the power of GraphQL to BuddyPress.

[![Build Status](https://travis-ci.org/wp-graphql/wp-graphql-buddypress.svg?branch=master)](https://travis-ci.org/wp-graphql/wp-graphql-buddypress )

Please, use and provide feedback!

Docs (soon) • <a href="https://ralv.es" target="_blank">Renato Alves</a> • <a href="https://wpgql-slack.herokuapp.com/" target="_blank">Join Slack</a>

## System Requirements

* PHP >= 7.1
* WP >= 4.8
* WPGraphQL >= latest
* BuddyPress >= latest

## Quick Install

1. Install & activate [BuddyPress](https://buddypress.org/)
2. Install & activate [WPGraphQL](https://www.wpgraphql.com/)
3. Clone or download the zip of this repository into your WordPress plugin directory & activate the **WPGraphQL BuddyPress** plugin
4. (Optional) Install & activate [WPGraphQL-JWT-Authentication](https://github.com/wp-graphql/wp-graphql-jwt-authentication) to add a `login` mutation that returns a JSON Web Token.
5. (Optional) Install & activate [WPGraphQL-CORS](https://github.com/funkhaus/wp-graphql-cors) to add an extra layer of security using HTTP CORS and some of WPGraphQL advanced functionality.

## Components Supported

- [x] Groups (with Avatar and Cover Attachments)
- [x] Members (with Avatar and Cover Attachments)
- [x] Blogs (with Avatar and Cover Attachments)
- [x] XProfile (Groups, Fields and Data)
- [x] Friends

## Pending Components

- [ ] Components
- [ ] Notifications
- [ ] Signup
- [ ] Activity
- [ ] Messages
- [ ] Group Membership
- [ ] Group Membership Request(s)
- [ ] Group Invites
