# Vehicle Management System

Database Management System Lab project.

A web app for businesses that run vehicle fleets, like delivery companies and rental agencies. It keeps all vehicle records in one place: maintenance, fuel, trips, spare parts, compliance and bookings.

## Features

* **Vehicles.** Add, edit, delete and search vehicles by make, model or year.
* **Maintenance.** Log services and repair costs for each vehicle.
* **Fuel logs.** Record fuel bought, cost per liter and odometer readings.
* **Trips.** Assign drivers to trips with start and end locations.
* **Inventory.** Track spare parts and flag stock that is running low.
* **Compliance.** Track insurance, inspections and tax due dates.
* **Reservations.** Book vehicles for set dates.
* **Dashboard.** Charts for fuel efficiency and maintenance costs.

## Tech stack

* Frontend: HTML, Tailwind CSS, JavaScript, Chart.js
* Backend: PHP REST API
* Database: MySQL

## Database

The database has 8 tables and 2 views.

| Tables | Views |
| --- | --- |
| Vehicle, Driver, Maintenance, FuelLog, Trip, Inventory, Compliance, Reservation | FuelEfficiency (km per liter), LowInventory (parts at or below reorder level) |

The SQL file creates the database and adds sample data.

## Project structure

```
index.html     Frontend (all pages and the dashboard)
api/           PHP endpoints, one for each module
database/      SQL file with tables, views and sample data
images/        Background image
docs/          Project proposal
```

## How to run

1. Install [XAMPP](https://www.apachefriends.org) and start Apache and MySQL.
2. Copy this folder into `xampp/htdocs/`.
3. Open phpMyAdmin and import `database/vehicle_management.sql`.
4. Open `http://localhost/Vehicle-Management-System/` in your browser.

The API connects to MySQL as `root` with no password, which is the XAMPP default. If your setup is different, change the login line at the top of each file in `api/`.

## Team

* Muhammad Abdullah
* Abdullah Khalid
