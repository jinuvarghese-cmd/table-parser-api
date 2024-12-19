
# Table Parser API

This is a simple API built with Laravel that accepts a website URL, parses any tables found on the page, and returns the content of those tables in JSON format.

## Requirements

- PHP >= 7.4
- Composer
- Laravel 8.x or higher

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your-username/your-repo.git
   ```

2. Navigate to the project directory:

   ```bash
   cd your-repo
   ```

3. Install the dependencies via Composer:

   ```bash
   composer install
   ```

4. Set up the environment file:

   ```bash
   cp .env.example .env
   ```

5. Generate the application key:

   ```bash
   php artisan key:generate
   ```

6. Set up caching (optional):
   - Ensure your cache system is configured correctly (e.g., `file`, `redis`, etc.) in the `.env` file.

7. Run the server:

   ```bash
   php artisan serve
   ```

   This will start your Laravel development server at `http://localhost:8000`.

## API Documentation

### **Base URL**

`/api/parse-tables`

---

### **Endpoint: `POST /api/parse-tables`**

#### Description:
This API accepts a website URL and parses any tables found on that page. It returns the content of those tables in JSON format.

---

#### **Request:**

- **Method**: `POST`
- **Content-Type**: `application/json`

**Request Body**:

```json
{
    "url": "https://example.com"
}
```

- `url` (required): The website URL to be parsed. The URL must be valid and well-formed.

---

#### **Response:**

##### **Success (200 OK):**

If tables are found and parsed successfully:

```json
{
    "message": "Tables parsed successfully!",
    "tables": [
        [
            ["Header 1", "Header 2"],
            ["Row 1 Col 1", "Row 1 Col 2"],
            ["Row 2 Col 1", "Row 2 Col 2"]
        ]
    ]
}
```

- `message`: A success message indicating that the tables were parsed.
- `tables`: An array of tables, where each table is represented as an array of rows, and each row contains an array of cell values.

##### **No Tables Found (404 Not Found):**

If no tables are found on the provided URL:

```json
{
    "message": "No tables found on the provided website."
}
```

##### **Failure (502 Bad Gateway):**

If the website request fails (e.g., invalid URL or HTTP error):

```json
{
    "message": "Failed to fetch website content.",
    "status": 502
}
```

##### **Invalid URL (400 Bad Request):**

If the URL provided is invalid:

```json
{
    "message": "The URL is invalid.",
    "errors": {
        "url": ["The url field must be a valid URL."]
    }
}
```

##### **Server Error (500 Internal Server Error):**

If there's an issue processing the request (e.g., unexpected errors):

```json
{
    "message": "An error occurred while processing the website.",
    "error": "Detailed error message here"
}
```

---

### **Possible Error Codes:**

- **400** - Bad Request: Invalid URL format.
- **404** - Not Found: No tables found on the given URL.
- **502** - Bad Gateway: Failed to fetch website content.
- **500** - Internal Server Error: Server-side error while processing.

---

### **Rate Limiting** (Optional):

If your API becomes publicly available, consider adding rate limiting to prevent abuse. For example, limit requests to 60 per minute.

---

### **Caching**:

- The parsed data for a given URL is cached for **10 minutes**. Subsequent requests for the same URL within this period will return cached results, improving performance.

---

### **How to Use:**

1. **Send a POST request** to `/api/parse-tables` with the `url` parameter.
2. **Receive a JSON response** containing either the parsed table data or an appropriate error message.

---

### **Example Request:**

```bash
POST /api/parse-tables HTTP/1.1
Host: example.com
Content-Type: application/json

{
    "url": "https://example.com"
}
```

---

### **Example Response:**

```json
{
    "message": "Tables parsed successfully!",
    "tables": [
        [
            ["Header 1", "Header 2"],
            ["Row 1 Col 1", "Row 1 Col 2"],
            ["Row 2 Col 1", "Row 2 Col 2"]
        ]
    ]
}
```

---

### **Project Structure**

```
/app
    /Http
        /Controllers
            TableParserController.php
/routes
    api.php
    web.php
```

---

## License

This project is licensed under the MIT License.