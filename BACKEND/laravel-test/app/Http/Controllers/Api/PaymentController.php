<?php
    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    use App\Models\Transaction;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\DB;

    /**
     * @OA\Info(title="Payment API", version="1.0")
     */
    class PaymentController extends Controller
    {
    /**
     * @OA\Post(
     *     path="/api/process",
     *     summary="Process payment with EasyMoney",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "currency"},
     *             @OA\Property(property="amount", type="number", format="float", example=100.00),
     *             @OA\Property(property="currency", type="string", example="USD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pago procesado con éxito")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in payment",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en el pago")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Connection error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la conexión")
     *         )
     *     )
     * )
     */
        public function payWithEasyMoney(Request $request)
        {
            $amount = $request->input('amount');
            $currency = $request->input('currency');

            // Redondear el monto a entero para EasyMoney
            $roundedAmount = (int) round($amount);

            // Guardar la transacción inicial
            $transaction = Transaction::create([
                'payment_system' => 'easy_money',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
            ]);

            try {
                // Realizar la llamada a la API de EasyMoney
                $response = Http::post('https://easymoney.local/process', [
                    'amount' => $roundedAmount,
                    'currency' => $currency,
                ]);

                // Guardar el log de la request
                $this->logRequest('/process', $response->json());

                if ($response->successful()) {
                    $transaction->update(['status' => 'success']);
                    return response()->json(['message' => 'Pago procesado con éxito']);
                } else {
                    $transaction->update(['status' => 'failed']);
                    return response()->json(['message' => 'Error en el pago'], 400);
                }
            } catch (\Exception $e) {
                $transaction->update(['status' => 'failed']);
                return response()->json(['message' => 'Error en la conexión'], 500);
            }
        }

            /**
     * @OA\Post(
     *     path="/api/payment/superwalletz",
     *     summary="Process payment with SuperWalletz",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "currency"},
     *             @OA\Property(property="amount", type="number", format="float", example=100.00),
     *             @OA\Property(property="currency", type="string", example="USD")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment initiated, waiting for confirmation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pago iniciado, esperando confirmación")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error starting payment",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al iniciar el pago")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Connection error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error en la conexión")
     *         )
     *     )
     * )
     */
        public function payWithSuperWalletz(Request $request)
        {
            $amount = $request->input('amount');
            $currency = $request->input('currency');
            $callbackUrl = route('webhook.superwalletz');

            $transaction = Transaction::create([
                'payment_system' => 'super_walletz',
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
            ]);

            try {
                // Realizar la llamada a la API de SuperWalletz
                $response = Http::post('https://superwalletz.local/pay', [
                    'amount' => $amount,
                    'currency' => $currency,
                    'callback_url' => $callbackUrl,
                ]);

                // Guardar el log de la request
                $this->logRequest('/pay', $response->json());

                if ($response->successful()) {
                    $transaction->update(['transaction_id' => $response['transaction_id']]);
                    return response()->json(['message' => 'Pago iniciado, esperando confirmación']);
                } else {
                    $transaction->update(['status' => 'failed']);
                    return response()->json(['message' => 'Error al iniciar el pago'], 400);
                }
            } catch (\Exception $e) {
                $transaction->update(['status' => 'failed']);
                return response()->json(['message' => 'Error en la conexión'], 500);
            }
        }


    /**
     * @OA\Post(
     *     path="/api/payment/superwalletz/webhook",
     *     summary="Webhook for SuperWalletz payment status",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"transaction_id", "status"},
     *             @OA\Property(property="transaction_id", type="string", example="12345"),
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Webhook processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Webhook procesado con éxito")
     *         )
     *     )
     * )
     */
        public function webhookSuperWalletz(Request $request)
        {
            $data = $request->all();

            // Guardar el log del webhook
            $this->logRequest('/webhook/superwalletz', $data);

            // Actualizar el estado de la transacción
            $transaction = Transaction::where('transaction_id', $data['transaction_id'])->first();
            if ($transaction) {
                $transaction->update(['status' => $data['status']]);
            }

            return response()->json(['message' => 'Webhook procesado con éxito']);
        }

        private function logRequest($endpoint, $body)
        {
            DB::table('request_logs')->insert([
                'endpoint' => $endpoint,
                'request_body' => json_encode($body),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }