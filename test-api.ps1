$login = Invoke-RestMethod -Uri "http://localhost:8000/api/auth/login" -Method POST -Body (@{email="admin@salesmanagement.com";password="password"}|ConvertTo-Json) -ContentType "application/json"
$headers = @{Authorization="Bearer $($login.token)";Accept="application/json"}
$customer = Invoke-RestMethod -Uri "http://localhost:8000/api/customers/1" -Headers $headers
Write-Host "`n=== JSON COMPLETO ===`n" -ForegroundColor Green
$customer.data | ConvertTo-Json -Depth 5
