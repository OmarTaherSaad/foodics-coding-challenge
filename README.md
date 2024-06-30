Foodics Backend Coding Challenge

## Overview

This is a mini backend application that's going to manage the orders, products, and ingredients of Foodics. It shall be able to add orders to inventory levels for products, decrement inventory levels upon order creation, and send out a notification whenever the stock level reaches a threshold value for an ingredient.

## Setup Instructions

### Prerequisites

-   PHP 8.1+
-   Composer
-   MySQL or any other supported database

#### Database Character Set and Collation

I used the utf8mb4 character set along with the utf8mb4_unicode_ci collation for our database to ensure good support for most characters and languages.

##### Why utf8mb4 and utf8mb4_unicode_ci?

Full Unicode Support: Stores all Unicode characters from Emojis to special symbols.

Case Insensitivity: String comparisons are case-insensitive improving the user experience.

Multi-language Compatibility: Virtually all languages are supported—ensuring consistent behavior across an application.

Such settings enable our application to recognize most characters and languages.

### Installation

1.  **Clone the repository:**

    sh
    Copy code
    `git clone https://github.com/OmarTaherSaad/foodics-coding-challenge`

2.  **Navigate to the project directory:**

        sh

    Copy code

        `cd foodics-coding-challenge`

3.  **Install dependencies:**

    sh

    Copy code

    `composer install`

4.  **Copy the `.env.example` file to `.env` and configure your environment variables:**

    sh

    Copy code

    ` cp .env.example .env`

    - Set your database credential

-   Set the `MERCHANT_EMAIL` to the email address where notifications should be sent

5. **Generate an application key:**

    sh

    Copy code

    `php artisan key:generate`

6. **Run the migrations and seed the database:**
   sh
    Copy code

`php artisan migrate --seed`



### Run the Application

To set up the application locally, you should run it in PHP's built-in HTTP development server. To do this, execute the following command:

sh

Copy code

`php artisan serve`



## Running Tests

### Unit Tests

They correspond to tests for individual components such as models, services, or controllers.



### Feature Tests

It has feature tests that cover end-to-end scenarios to ensure that the application works.

### Run the Test Suite

To run the complete test suite:

sh
Copy code

`php artisan test`

## API Documentation

### Create Order

**Endpoint:**

bash
Copy code

`POST /api/orders`
**Request Body:**

json
Copy code

`{

"products": [
{
"product_id": "uuid-of-product",
"quantity": 1
}
]
}`
**Response:**

-   **Success:**
    json  
      
    Copy code
     
    `{ 
      "success": true, 
      "message": "Order placed successfully", 
      "order": { 
         "id": "uuid-of-order", 
         "created_at": "2023-12-01T12:00:00.000000Z",
"products": [
          {
            "id": "uuid-of-product",
            "name": "Product Name",
            "quantity": 1
mutable ?
        ]
      }
    }
`

-   **Error (Product not found):**
        json

        Copy code

        `
    "success": false,
    "error": "Product not found: uuid-of-product"
    }`
-   **Error (Insufficient stock):**
        json

        Copy code

        `{
          "success": false,
    "error": "Insufficient stock for ingredient: Ingredient Name"
    }
    `

## Project Structure

### Models

-   **Order**: This is an order.

-   **Product**: This is a product.

-   **Ingredient**: This is an ingredient that goes into products.

### Services

-   **OrderService**: Encapsulates all business logic related to processing orders.

-   **OrderController**: This handles the creation of orders and interacts with the `OrderService`.

### Notifications

-   **IngredientLowStockNotification**: This sends an email notification whenever an ingredient's stock falls below 50%.

## Assumptions and Decisions

-   **Stock Levels**: The stock levels for ingredients are stored in grams.

-   **Notification**: A notification will be sent to the merchant's email when the stock of any ingredient reaches below 50% of its default stock.
-   **UUIDS**: All primary keys are UUIDs for uniqueness and scalability.

## Security Considerations

-   **Validation and Sanitization**: All inputs are validated and sanitized to prevent security vulnerabilities.

-   **Environment Variables**: Storing sensitive data within environment variables.

## Performance Optimizations

-   **Eager Loading**: The use of eager loading avoids the N+1 problem, thus optimizes database queries.

-   **Caching**: Proper places implemented to decrease load over databases and improve response times.

## Contact

In case of questions or problems please contact:

-   **Full Name**: Omar Taher Saad

-   **Email**: omartahersaad@outlook.com
