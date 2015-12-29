# LQCenter Scores
Web-based score tracking system for Laser Sport

## History
This project was initally written as a web-based interface for reporting scores for Laser Quest tournaments. It has been published here at the request of @rvegajr to continue the project.

The project contains a deprecated MySQL API and should be re-written to prevent against SQL injection. The database schema doesn't use referential integrity and needs significant normalization.

## Getting Started
A simple LAMP stack can host the project. The database dump (`lqcenter_scores.sql`) is included and needs to be imported. A row in the table `web_admin` should be added or the existing row modified for the administrative functions. To begin entering scores, an event row must be added to the table `web_event`.

## License
LQCenter Scores is free; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
