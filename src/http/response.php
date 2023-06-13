<?php

namespace App\Http;

use App\interface\ResponseInterface;

/**
 * Class Response to send response from entry request
 */
class Response implements ResponseInterface
{
      private int $status;
      private string $statusText;

      public function setStatus(int $status): ?self
      {
            try {
                  if (!in_array($status, Response::HTTP_CODE_RESPONSES)) {
                        throw new \Exception("Invalid status code send $status", 1);
                  }
                  $this->status = $status;
                  http_response_code($status);
                  return $this;
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function getStatus(): ?int
      {
            return $this->status;
      }

      public function setStatusText(int $statusText = null): self
      {
            $this->statusText = $statusText;
            return $this;
      }

      public function getStatusText(): ?string
      {
            return $this->statusText;
      }

      public function json(mixed $data): ?self
      {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);

            return $this;
      }

      public function close(): void
      {
            exit;
      }
}
