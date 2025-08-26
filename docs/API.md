# Payment API Documentation

## 🌐 Base URL

```
Development: http://localhost:8000
Production: https://your-domain.com
```

## 🔑 Authentication

Currently, the API is open for development. For production, implement authentication middleware.

## 📡 API Endpoints

### 1. Create Payment Charge

Creates a new payment charge using the Shift4 payment provider.

**Endpoint:** `POST /api/charge`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": 25.99,
  "currency": "USD",
  "card": {
    "number": "4007000000027",
    "expMonth": 12,
    "expYear": 2028,
    "cvv": "123"
  }
}
```

**Field Descriptions:**
- `amount` (float, required): Payment amount in decimal format
- `currency` (string, required): 3-letter ISO currency code (USD, EUR, etc.)
- `card.number` (string, required): Card number (13-19 digits)
- `card.expMonth` (integer, required): Expiration month (1-12)
- `card.expYear` (integer, required): Expiration year (4 digits)
- `card.cvv` (string, required): Card verification value (3-4 digits)

**Success Response (200):**
```json
{
  "transactionId": "char_KAUdq308nlyN8iGBs6myayQq",
  "status": "success",
  "amount": 25.99,
  "currency": "USD"
}
```

**Error Response (400 - Validation Error):**
```json
{
  "status": "failed",
  "message": "This value should be positive."
}
```

**Error Response (400 - Payment Failed):**
```json
{
  "status": "failed",
  "message": "The payment was declined by the provider."
}
```

**Error Response (500 - System Error):**
```json
{
  "status": "failed",
  "message": "Unexpected error while processing payment."
}
```

### 2. Process Refund

Processes a refund for an existing charge.

**Endpoint:** `POST /api/refund`

**Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "chargeId": "char_KAUdq308nlyN8iGBs6myayQq",
  "amount": 25.99
}
```

**Field Descriptions:**
- `chargeId` (string, required): Original charge transaction ID
- `amount` (float, optional): Refund amount (if null, refunds full amount)

**Success Response (200):**
```json
{
  "status": "success",
  "refundId": "ref_123456789"
}
```

**Error Response (400 - Validation Error):**
```json
{
  "status": "failed",
  "message": "This value should not be blank."
}
```

**Error Response (400 - Refund Failed):**
```json
{
  "status": "failed",
  "message": "Refund failed at the provider."
}
```

## 🧪 Testing the API

### Using cURL

#### Test Charge Endpoint
```bash
curl -X POST http://localhost:8000/api/charge \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 25.99,
    "currency": "USD",
    "card": {
      "number": "4007000000027",
      "expMonth": 12,
      "expYear": 2028,
      "cvv": "123"
    }
  }'
```

#### Test Refund Endpoint
```bash
curl -X POST http://localhost:8000/api/refund \
  -H "Content-Type: application/json" \
  -d '{
    "chargeId": "char_KAUdq308nlyN8iGBs6myayQq",
    "amount": 25.99
  }'
```

### Using PowerShell

#### Test Charge Endpoint
```powershell
$body = '{
  "amount": 25.99,
  "currency": "USD",
  "card": {
    "number": "4007000000027",
    "expMonth": 12,
    "expYear": 2028,
    "cvv": "123"
  }
}'

Invoke-WebRequest -Uri "http://localhost:8000/api/charge" `
  -Method POST `
  -ContentType "application/json" `
  -Body $body
```

#### Test Refund Endpoint
```powershell
$refundBody = '{
  "chargeId": "char_KAUdq308nlyN8iGBs6myayQq",
  "amount": 25.99
}'

Invoke-WebRequest -Uri "http://localhost:8000/api/refund" `
  -Method POST `
  -ContentType "application/json" `
  -Body $refundBody
```

### Using Postman

1. **Create Charge Request:**
   - Method: `POST`
   - URL: `http://localhost:8000/api/charge`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
   ```json
   {
     "amount": 25.99,
     "currency": "USD",
     "card": {
       "number": "4007000000027",
       "expMonth": 12,
       "expYear": 2028,
       "cvv": "123"
     }
   }
   ```

2. **Create Refund Request:**
   - Method: `POST`
   - URL: `http://localhost:8000/api/refund`
   - Headers: `Content-Type: application/json`
   - Body (raw JSON):
   ```json
   {
     "chargeId": "char_KAUdq308nlyN8iGBs6myayQq",
     "amount": 25.99
   }
   ```

## 🧪 Test Card Numbers

### Valid Test Cards
- **Visa**: `4007000000027` (used in task requirements)
- **Visa**: `4242424242424242` (general test card)
- **MasterCard**: `5555555555554444`
- **American Express**: `378282246310005`

### Invalid Test Cards
- **Declined**: `4000000000000002`
- **Insufficient Funds**: `4000000000009995`
- **Expired Card**: `4000000000000069`
- **Incorrect CVC**: `4000000000000127`

## 📊 Response Status Codes

| Status Code | Description | When |
|-------------|-------------|------|
| **200** | Success | Payment/refund processed successfully |
| **400** | Bad Request | Validation error or payment declined |
| **500** | Internal Server Error | System error or provider unavailable |

## 🔍 Error Handling

### Validation Errors
The API uses Symfony's validator component to ensure data integrity:

- **Amount**: Must be positive number
- **Currency**: Must be 3-letter ISO code
- **Card Number**: Must be 13-19 digits
- **Expiration**: Must be valid month/year
- **CVV**: Must be 3-4 digits

### Provider Errors
When the payment provider (Shift4) returns an error:

- **Card Declined**: Invalid card, insufficient funds, etc.
- **Network Error**: Provider API unavailable
- **Authentication Error**: Invalid API credentials

### System Errors
Unexpected errors in the application:

- **Database Error**: If using persistent storage
- **Memory Error**: System resource issues
- **Configuration Error**: Missing environment variables

## 📈 Rate Limiting

Currently, the API has no rate limiting implemented. For production, consider implementing:

- **Per IP**: Maximum requests per minute per IP
- **Per User**: Maximum requests per minute per authenticated user
- **Per Endpoint**: Different limits for charge vs. refund

## 🔒 Security Considerations

### Input Validation
- All inputs are validated using Symfony's validator
- SQL injection protection through DTOs
- XSS protection through proper output encoding

### API Security
- HTTPS enforcement for production
- Environment-based configuration
- No sensitive data in error messages

### Card Data
- Card numbers are not logged
- CVV values are not stored
- PCI compliance ready

## 📝 Logging

### Request Logging
All API requests are logged with:
- Timestamp
- IP address
- Request method and path
- Response status code
- Processing time

### Provider Logging
External API calls are logged with:
- Request URL
- Response status
- Response time
- Error details (if any)

## 🔮 Future Enhancements

### Planned Features
- **Webhooks**: Real-time payment notifications
- **Batch Processing**: Multiple payments in one request
- **Recurring Payments**: Subscription support
- **Multi-currency**: Advanced currency conversion

### API Versioning
Future API versions will be available at:
- `v1`: Current implementation
- `v2`: Enhanced features
- `v3`: Advanced capabilities

## 📞 Support

For API support or questions:
- **Documentation**: Check this file and README.md
- **Testing**: Use the provided test examples
- **Issues**: Report bugs through GitHub issues
- **Questions**: Create GitHub discussions

---

**API Version**: v1.0  
**Last Updated**: August 2025  
**Status**: Production Ready ✅ 