package com.example;

import software.amazon.awssdk.auth.credentials.ProfileCredentialsProvider;
import software.amazon.awssdk.regions.Region;
import software.amazon.awssdk.services.dynamodb.DynamoDbClient;
import software.amazon.awssdk.services.dynamodb.model.AttributeValue;
import software.amazon.awssdk.services.dynamodb.model.DynamoDbException;
import software.amazon.awssdk.services.dynamodb.model.PutItemRequest;
import software.amazon.awssdk.services.dynamodb.model.PutItemResponse;
import software.amazon.awssdk.services.dynamodb.model.ResourceNotFoundException;
import java.util.HashMap;

public class PutItems {

  public static void putItemInTable(DynamoDbClient ddb,
      String tableName,
      String artist,
      String artistVal,
      String title,
      String titleVal,
      String year,
      String yearValue,
      String web_url,
      String web_urlValue,
      String image_url,
      String image_urlValue) {

    HashMap<String, AttributeValue> itemValues = new HashMap<>();
    itemValues.put(artist, AttributeValue.builder().s(artistVal).build());
    itemValues.put(title, AttributeValue.builder().s(titleVal).build());
    itemValues.put(year, AttributeValue.builder().s(yearValue).build());
    itemValues.put(web_url, AttributeValue.builder().s(web_urlValue).build());
    itemValues.put(image_url, AttributeValue.builder().s(image_urlValue).build());

    PutItemRequest request = PutItemRequest.builder()
        .tableName(tableName)
        .item(itemValues)
        .build();

    try {
      PutItemResponse response = ddb.putItem(request);
      System.out.println(
          tableName + " was successfully updated. The request id is " + response.responseMetadata().requestId());

    } catch (ResourceNotFoundException e) {
      System.err.format("Error: The Amazon DynamoDB table \"%s\" can't be found.\n", tableName);
      System.err.println("Be sure that it exists and that you've typed its name correctly!");
      System.exit(1);
    } catch (DynamoDbException e) {
      System.err.println(e.getMessage());
      System.exit(1);
    }
  }
}