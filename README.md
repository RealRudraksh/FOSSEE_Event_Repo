# \# Event Registration Module (Drupal 10)

# 

# A custom Drupal 10 module that allows students to register for events, prevents duplicate registrations, and provides an admin dashboard for management.

# 

# \## Features

# \* \*\*Event Configuration\*\*: Admins can create events with specific dates and categories.

# \* \*\*User Registration\*\*: Students can register using a form with AJAX-dependent fields.

# \* \*\*Validation\*\*: Prevents duplicate registrations (Same Email + Same Event Date).

# \* \*\*Email Notifications\*\*: Sends confirmation emails to students and admins.

# \* \*\*Admin Dashboard\*\*: View all registrations with filters and Export to CSV.

# 

# \## Installation Steps

# 1\.  Copy the `event\_registration` folder to your Drupal modules directory: `/modules/custom/`.

# 2\.  Enable the module via the Extend menu or Drush: `drush en event\_registration`.

# 3\.  \*\*Import Database Tables\*\*:

# &nbsp;   \* Go to your database (phpMyAdmin).

# &nbsp;   \* Import the `custom\_tables.sql` file provided in this repository.

# &nbsp;   \* \*This creates the necessary `event\_config` and `event\_registration` tables.\*

# 

# \## URLs \& Usage

# | Page | URL Path | Permission Required |

# | :--- | :--- | :--- |

# | \*\*User Registration Form\*\* | `/event-registration` | Access content |

# | \*\*Create New Events\*\* | `/admin/config/event-registration/manage` | Administer event settings |

# | \*\*Email Settings\*\* | `/admin/config/event-registration/settings` | Administer event settings |

# | \*\*Admin Dashboard (List/CSV)\*\* | `/admin/event-registration/list` | Administer event settings |

# 

# \## Database Schema

# The module uses two custom tables:

# 1\.  \*\*`event\_config`\*\*: Stores event details (ID, Name, Date, Category, Registration Start/End).

# 2\.  \*\*`event\_registration`\*\*: Stores student data (ID, Name, Email, College, Dept, Timestamp).

# 

# \## Validation Logic

# \* \*\*AJAX\*\*: The "Event Name" dropdown dynamically updates based on the selected "Event Date" and "Category".

# \* \*\*Duplicates\*\*: On submission, the system checks the database for an existing record with the same `email` and `event\_date`.

# \* \*\*Input\*\*: Email format is validated, and special characters are stripped from text fields.

# 

# \## Known Limitations (Localhost)

# \* \*\*Email Delivery\*\*: This module uses Drupal's standard `MailManager`. On local environments (like XAMPP) without a configured SMTP server, emails may not be delivered to the inbox, but the logic can be verified in \*\*Reports > Recent Log Messages\*\*.

