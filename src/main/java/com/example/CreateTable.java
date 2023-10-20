package com.example;

import software.amazon.awssdk.auth.credentials.ProfileCredentialsProvider;
import software.amazon.awssdk.regions.Region;
import software.amazon.awssdk.services.dynamodb.model.DynamoDbException;
import software.amazon.awssdk.services.dynamodb.model.AttributeDefinition;
import software.amazon.awssdk.services.dynamodb.model.CreateTableRequest;
import software.amazon.awssdk.services.dynamodb.model.CreateTableResponse;
import software.amazon.awssdk.services.dynamodb.model.KeySchemaElement;
import software.amazon.awssdk.services.dynamodb.model.KeyType;
import software.amazon.awssdk.services.dynamodb.model.ProvisionedThroughput;
import software.amazon.awssdk.services.dynamodb.model.ScalarAttributeType;
import software.amazon.awssdk.services.dynamodb.DynamoDbClient;

public class CreateTable {

  public void createTable() {

    String tableName = "Music";
    ProfileCredentialsProvider credentialsProvider = ProfileCredentialsProvider.create();
    Region region = Region.US_EAST_1;
    DynamoDbClient ddb = DynamoDbClient.builder()
        .credentialsProvider(credentialsProvider)
        .region(region)
        .build();

    System.out.format("Creating Amazon DynamoDB table %s\n with a composite primary key:\n", tableName);
    System.out.format("* Artist - partition key\n");
    System.out.format("* Title - sort key\n");
    String tableId = createTableComKey(ddb,
        tableName);
    System.out.println("The Amazon DynamoDB table Id value is " + tableId);
    ddb.close();
  }


  public static String createTableComKey(DynamoDbClient ddb, String tableName) {
    CreateTableRequest request = CreateTableRequest.builder()
        .attributeDefinitions(AttributeDefinition.builder()
            .attributeName("artist")
            .attributeType(ScalarAttributeType.S)
            .build(),
            AttributeDefinition.builder()
                .attributeName("title")
                .attributeType(ScalarAttributeType.S)
                .build())
        .keySchema(KeySchemaElement.builder()
            .attributeName("artist")
            .keyType(KeyType.HASH)
            .build(),
            KeySchemaElement.builder()
                .attributeName("title")
                .keyType(KeyType.RANGE)
                .build())
        .provisionedThroughput(ProvisionedThroughput.builder()
            .readCapacityUnits(new Long(10))
            .writeCapacityUnits(new Long(10)).build())
        .tableName(tableName)
        .build();

    String tableId = "";
    try {
      CreateTableResponse result = ddb.createTable(request);
      tableId = result.tableDescription().tableId();
      return tableId;
    } catch (DynamoDbException e) {
      System.err.println(e.getMessage());
      System.exit(1);
    }
    return "";
  }

}
